<?php
//transaction tamamlandı abone teslimi son kez test edilecek hurdaya ayırma düzenlenecek
class DepoController extends BackendController {

    public function getDepogelen($hatirlatma_id=false) {
        if($hatirlatma_id)
            return View::make('depo.depogelen',array('hatirlatma_id'=>$hatirlatma_id))->with(array('title'=>'Depo Gelen Sayaçlar Bilgi Ekranı'));
        else
            return View::make('depo.depogelen')->with(array('title'=>'Depo Gelen Sayaçlar Bilgi Ekranı'));
    }

    public function postDepogelenlist() {
        $servisid=BackendController::getKullaniciServis(1);
        $hatirlatma_id=Input::get('hatirlatma_id');
        $netsiscari_id=Input::get('netsiscari_id');
        $hatirlatma = false;
        if($hatirlatma_id!="") {
            $hatirlatma = Hatirlatma::find($hatirlatma_id);
        }
        if($hatirlatma){
            $query = DepoGelen::where('depogelen.id',$hatirlatma->depogelen_id)->whereIn('depogelen.servis_id',$servisid)
                ->where('servisstokkod.koddurum',1)->where('depogelen.durum','<>',2)->where('periyodik',0)
                ->select(array("depogelen.id","depogelen.fisno","netsiscari.cariadi","servisstokkod.stokadi","depogelen.adet","servis.servisadi","depogelen.kullanici",
                    "depogelen.tarih","depogelen.gtarih","netsiscari.ncariadi","servisstokkod.nstokadi","servis.nservisadi","depogelen.nkullanici","depogelen.carikod",
                    "depogelen.kayitdurum","depogelen.servis_id"))
                ->leftjoin("netsiscari", "depogelen.carikod", "=", "netsiscari.carikod")
                ->leftjoin("servis", "depogelen.servis_id", "=", "servis.id")
                ->leftjoin("servisstokkod", "depogelen.servisstokkodu", "=", "servisstokkod.stokkodu");

        }else if($netsiscari_id!=""){
            $netsiscarilist=explode(',',$netsiscari_id);
            $netsiscariler=NetsisCari::whereIn('id',$netsiscarilist)->get(array('carikod'))->toArray();
            //$sube=Sube::whereIn('netsiscari_id',$netsiscarilist)->where('aktif',1)->first();
            //if($sube) { //TODO düzenlenecek
                $query = DepoGelen::whereIn('depogelen.servis_id',$servisid)->where('servisstokkod.koddurum',1)->where('depogelen.durum','<>',2)
                    ->whereIn('depogelen.carikod',$netsiscariler)->where('periyodik',0)
                    ->select(array("depogelen.id","depogelen.fisno","netsiscari.cariadi","servisstokkod.stokadi","depogelen.adet","servis.servisadi","depogelen.kullanici",
                        "depogelen.tarih","depogelen.gtarih","netsiscari.ncariadi","servisstokkod.nstokadi","servis.nservisadi","depogelen.nkullanici","depogelen.carikod",
                        "depogelen.kayitdurum","depogelen.servis_id"))
                    ->leftjoin("netsiscari", "depogelen.carikod", "=", "netsiscari.carikod")
                    ->leftjoin("servis", "depogelen.servis_id", "=", "servis.id")
                    ->leftjoin("servisstokkod", "depogelen.servisstokkodu", "=", "servisstokkod.stokkodu");
            /*}else{
                $query = DepoGelen::whereIn('depogelen.servis_id',$servisid)->where('servisstokkod.koddurum',1)->where('depogelen.durum','<>',2)->where('periyodik',0)
                    ->select(array("depogelen.id","depogelen.fisno","netsiscari.cariadi","servisstokkod.stokadi","depogelen.adet","servis.servisadi","depogelen.kullanici",
                        "depogelen.tarih","depogelen.gtarih","netsiscari.ncariadi","servisstokkod.nstokadi","servis.nservisadi","depogelen.nkullanici","depogelen.carikod",
                        "depogelen.kayitdurum","depogelen.servis_id"))
                    ->leftjoin("netsiscari", "depogelen.carikod", "=", "netsiscari.carikod")
                    ->leftjoin("servis", "depogelen.servis_id", "=", "servis.id")
                    ->leftjoin("servisstokkod", "depogelen.servisstokkodu", "=", "servisstokkod.stokkodu");
            }*/

        }else{
            $query = DepoGelen::whereIn('depogelen.servis_id',$servisid)->where('servisstokkod.koddurum',1)->where('depogelen.durum','<>',2)->where('periyodik',0)
                ->select(array("depogelen.id","depogelen.fisno","netsiscari.cariadi","servisstokkod.stokadi","depogelen.adet","servis.servisadi","depogelen.kullanici",
                    "depogelen.tarih","depogelen.gtarih","netsiscari.ncariadi","servisstokkod.nstokadi","servis.nservisadi","depogelen.nkullanici","depogelen.carikod",
                    "depogelen.kayitdurum","depogelen.servis_id"))
                ->leftjoin("netsiscari", "depogelen.carikod", "=", "netsiscari.carikod")
                ->leftjoin("servis", "depogelen.servis_id", "=", "servis.id")
                ->leftjoin("servisstokkod", "depogelen.servisstokkodu", "=", "servisstokkod.stokkodu");
        }
        return Datatables::of($query)
            ->editColumn('tarih', function ($model) {
                $date = new DateTime($model->tarih);
                return $date->format('d-m-Y');})
            ->addColumn('islemler', function ($model) use($netsiscari_id){
                $root = BackendController::getRootDizin();
                $userservisid=Auth::user()->servis_id;
                $arizakayitli = SayacGelen::where('depogelen_id',$model->id)->where('arizakayit',true)->first();
                if($userservisid==0 || ($userservisid==$model->servis_id) || ($userservisid=1 && ($model->servis_id==1 || $model->servis_id==3 || $model->servis_id==4)))
                    if($arizakayitli)
                        return "<a class='btn btn-sm btn-warning' href='".$root."/depo/depogelenduzenle/".$model->id."' > Düzenle </a>";
                    else
                        return "<a class='btn btn-sm btn-warning' href='".$root."/depo/depogelenduzenle/".$model->id."' > Düzenle </a>
                <a href='#portlet-delete' data-toggle='modal' data-id='".$model->id."' class='btn btn-sm btn-danger delete' data-original-title='' title=''>Sil</a>";
                else
                    return "<a class='btn btn-sm btn-info detay' href='#detay-goster' data-toggle='modal' data-id='{$model->id}'> Detay </a>";
            })
            ->make(true);
    }

    public function getGelenbilgi() {
        try {
            $id=Input::get('id');
            $depogelen = DepoGelen::find($id);
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

    public function getDepoteslim($hatirlatma_id=false) {
        if($hatirlatma_id)
            return View::make('depo.depoteslim',array('hatirlatma_id'=>$hatirlatma_id))->with(array('title'=>'Depo Teslim Bilgi Ekranı'));
        else
            return View::make('depo.depoteslim')->with(array('title'=>'Depo Teslim Bilgi Ekranı'));
    }

    public function postDepoteslimlist() {
        $servis=BackendController::getKullaniciServis();
        $hatirlatma_id=Input::get('hatirlatma_id');
        $netsiscari_id=Input::get('netsiscari_id');
        $hatirlatma = false;
        if($hatirlatma_id!="") {
            $hatirlatma = Hatirlatma::find($hatirlatma_id);
        }
        if($hatirlatma){
            $query = DepoTeslim::where('depoteslim.netsiscari_id',$hatirlatma->netsiscari_id)->whereIn('depoteslim.servis_id',$servis)
                ->select(array("depoteslim.id","netsiscari.cariadi","servis.servisadi","depoteslim.sayacsayisi","depoteslim.gtipi","depoteslim.gdepodurum",
                    "kullanici.adi_soyadi","depoteslim.teslimtarihi","depoteslim.gteslimtarihi","netsiscari.ncariadi","servis.nservisadi","depoteslim.ntipi",
                    "depoteslim.ndepodurum","kullanici.nadi_soyadi"))
                ->leftjoin("netsiscari", "depoteslim.netsiscari_id", "=", "netsiscari.id")
                ->leftjoin("servis", "depoteslim.servis_id", "=", "servis.id")
                ->leftJoin('kullanici','depoteslim.kullanici_id','=','kullanici.id');

        }else if($netsiscari_id!=""){
            $netsiscarilist=explode(',',$netsiscari_id);
            $query = DepoTeslim::whereIn('depoteslim.servis_id',$servis)->whereIn('depoteslim.netsiscari_id',$netsiscarilist)
                ->select(array("depoteslim.id","netsiscari.cariadi","servis.servisadi","depoteslim.sayacsayisi","depoteslim.gtipi","depoteslim.gdepodurum",
                    "kullanici.adi_soyadi","depoteslim.teslimtarihi","depoteslim.gteslimtarihi","netsiscari.ncariadi","servis.nservisadi","depoteslim.ntipi",
                    "depoteslim.ndepodurum","kullanici.nadi_soyadi"))
                ->leftjoin("netsiscari", "depoteslim.netsiscari_id", "=", "netsiscari.id")
                ->leftjoin("servis", "depoteslim.servis_id", "=", "servis.id")
                ->leftJoin('kullanici','depoteslim.kullanici_id','=','kullanici.id');
        }else{
            $query = DepoTeslim::whereIn('depoteslim.servis_id',$servis)
                ->select(array("depoteslim.id","netsiscari.cariadi","servis.servisadi","depoteslim.sayacsayisi","depoteslim.gtipi","depoteslim.gdepodurum",
                    "kullanici.adi_soyadi","depoteslim.teslimtarihi","depoteslim.gteslimtarihi","netsiscari.ncariadi","servis.nservisadi","depoteslim.ntipi",
                    "depoteslim.ndepodurum","kullanici.nadi_soyadi"))
                ->leftjoin("netsiscari", "depoteslim.netsiscari_id", "=", "netsiscari.id")
                ->leftjoin("servis", "depoteslim.servis_id", "=", "servis.id")
                ->leftJoin('kullanici','depoteslim.kullanici_id','=','kullanici.id');
        }
        return Datatables::of($query)
            ->editColumn('teslimtarihi', function ($model) {
                if($model->teslimtarihi){
                    $date = new DateTime($model->teslimtarihi);
                    return $date->format('d-m-Y');
                }else{
                    return '';
                }
            })
            ->addColumn('islemler', function ($model) {
                if($model->gdepodurum==="Bekliyor")
                return "<a class='btn btn-sm btn-warning detay' href='#detay-goster' data-toggle='modal' data-id='{$model->id}'> Detay </a>
                <a href='#portlet-delete' data-toggle='modal' data-id='{$model->id}' class='btn btn-sm btn-danger delete' data-original-title='' title=''>Sil</a>";
                else
                return "<a class='btn btn-sm btn-warning detay' href='#detay-goster' data-toggle='modal' data-id='{$model->id}'> Detay </a>";
            })
            ->make(true);
    }

    public function postDepoteslim()
    {
        try{
            if(Input::has('teslim')){
                $depoteslimid=Input::get('teslim');
                $depoteslim = DepoTeslim::find($depoteslimid);
                if ($depoteslim) {
                    $depoteslim->netsiscari=NetsisCari::find($depoteslim->netsiscari_id);
                    $raporadi="TeslimTutanagi-".Str::slug($depoteslim->netsiscari->cariadi);
                    $export="pdf";
                    $kriterler=array();
                    $kriterler['id']=$depoteslimid;
                    JasperPHP::process( public_path('reports/teslimtutanak/teslimtutanak.jasper'), public_path('reports/outputs/teslimtutanak/'.$raporadi),
                        array($export), $kriterler,
                        Config::get('database.connections.report'))->execute();

                    if($export=='pdf'){
                        header("Content-type:application/pdf");
                        header("Content-Disposition:inline;filename=".$raporadi.".".$export."");
                    }else if($export=='xls'){
                        header("Content-Type:   application/vnd.ms-excel");
                        header("Content-Disposition: inline; filename=".$raporadi.".".$export."");
                    }else{
                        //header('Content-Type: application/octet-stream');
                        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
                        header("Content-Disposition: inline; filename=".$raporadi.".".$export."");
                    }
                    readfile("reports/outputs/teslimtutanak/".$raporadi.".".$export."");
                    File::delete("reports/outputs/teslimtutanak/".$raporadi.".".$export."");
                    return Redirect::back()->with(array('mesaj' => 'false'));
                } else {
                    return Redirect::back()->with(array('mesaj' => 'true', 'text' =>'Rapor alınacak teslimat seçilmedi', 'type' => 'warning','title' => 'Rapor Hatası'));
                }
            }else{
                $depoteslimid=Input::get('irsaliyeek');
                $depoteslim = DepoTeslim::find($depoteslimid);
                if ($depoteslim) {
                    $depoteslim->netsiscari=NetsisCari::find($depoteslim->netsiscari_id);
                    $raporadi="SayacListesi-".Str::slug($depoteslim->netsiscari->cariadi);
                    $export="pdf";
                    $kriterler=array();
                    $kriterler['id']=$depoteslimid;

                    JasperPHP::process( public_path('reports/sayaclistesi/sayaclistesi.jasper'), public_path('reports/outputs/sayaclistesi/'.$raporadi),
                        array($export), $kriterler,
                        Config::get('database.connections.report') )->execute();
                    if($export=='pdf'){
                        header("Content-type:application/pdf");
                        header("Content-Disposition:inline;filename=".$raporadi.".".$export."");
                    }else if($export=='xls'){
                        header("Content-Type:   application/vnd.ms-excel");
                        header("Content-Disposition: inline; filename=".$raporadi.".".$export."");
                    }else{
                        //header('Content-Type: application/octet-stream');
                        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
                        header("Content-Disposition: inline; filename=".$raporadi.".".$export."");
                    }
                    readfile("reports/outputs/sayaclistesi/".$raporadi.".".$export."");
                    File::delete("reports/outputs/sayaclistesi/".$raporadi.".".$export."");
                    return Redirect::back()->with(array('mesaj' => 'false'));
                } else {
                    return Redirect::back()->with(array('mesaj' => 'true', 'text' =>'Sayaç Listesi alınacak teslimat seçilmedi', 'type' => 'warning','title' => 'Rapor Hatası'));
                }
            }
        }catch (Exception $e){
            return Redirect::back()->with(array('mesaj' => 'true', 'text' =>'Rapor Alınırken Hata ile karşılaşıldı'.str_replace("'","\'",$e->getMessage()), 'type' => 'error','title' => 'Rapor Hatası'));
        }
    }

    public function getDepoteslimsil($id){
        try {
            DB::beginTransaction();
            $depoteslim = DepoTeslim::find($id);
            $bilgi = clone $depoteslim;
            if ($depoteslim) {
                $secilenler = $depoteslim->secilenler;
                $secilenlist = explode(',', $secilenler);
                $sayacgelenler = SayacGelen::whereIn('id', $secilenlist)->get();
                $sayacsayi = $depoteslim->sayacsayisi;
                try {
                    foreach ($sayacgelenler as $sayacgelen) {
                        $servistakip = ServisTakip::where('sayacgelen_id', $sayacgelen->id)->first();
                        $arizakayit = ArizaKayit::find($servistakip->arizakayit_id);
                        $arizafiyat = ArizaFiyat::find($servistakip->arizafiyat_id);
                        $degisenler = explode(',', $arizafiyat->degisenler);
                        $onaylanan = Onaylanan::find($servistakip->onaylanan_id);
                        $ucretlendirilen = Ucretlendirilen::find($servistakip->ucretlendirilen_id);
                        if($depoteslim->tipi==3){ //hurda
                            $hurda = Hurda::find($servistakip->hurda_id);
                            $servistakip->hurda_id = NULL;
                            $servistakip->hurdalamatarihi = NULL;
                            $servistakip->save();
                            if($hurda){
                                $hurda->delete();
                            }
                        }
                        $servistakip->onaylanan_id = NULL;
                        $servistakip->ucretlendirilen_id = NULL;
                        $servistakip->ucretlendirmetarihi = NULL;
                        $servistakip->gondermetarihi = NULL;
                        $servistakip->onaylanmatarihi = NULL;
                        $servistakip->reddetmetarihi = NULL;
                        $servistakip->tekrarucrettarihi = NULL;
                        $servistakip->gerigonderimtarihi = NULL;
                        $servistakip->depolararasitarihi = NULL;
                        $servistakip->sonislemtarihi = $servistakip->arizakayittarihi;
                        $servistakip->kullanici_id = $arizakayit->arizakayit_kullanici_id;
                        $servistakip->durum = 2;
                        $servistakip->save();
                        if ($ucretlendirilen){
                            if ($ucretlendirilen->sayacsayisi > 1) {
                                $ucretlendirilen->sayacsayisi--;
                                $ucretlendirilen->secilenler = BackendController::getListeFark(explode(',',$ucretlendirilen->secilenler), array($arizafiyat->id));
                                $ucretlendirilen->fiyat -= $arizafiyat->fiyat;
                                if (!is_null($arizafiyat->parabirimi2_id)) {
                                    $ucretlendirilen->fiyat2 -= $arizafiyat->fiyat2;
                                }
                                $ucretlendirilen->save();
                            }else{
                                if($onaylanan){
                                    $onaylanan->delete();
                                }
                                $ucretlendirilen->delete();
                            }
                        }
                        $ucretsiz = '';
                        $fiyatlar = BackendController::Fiyatlandir($degisenler, $sayacgelen->uretimyer_id);
                        $flag = 0;
                        for ($i = 0; $i < count($degisenler); $i++) {
                            $ucretsiz .= ($flag > 0 ? ',' : '') . '0';
                            $flag = 1;
                        }
                        $kdv = ($fiyatlar['total'] * 18) / 100;
                        $kdv2 = ($fiyatlar['total2'] * 18) / 100;
                        $arizafiyat->fiyatdurum = $fiyatlar['durum'];
                        $arizafiyat->degisenler = BackendController::Sort($arizafiyat->degisenler);
                        $arizafiyat->genel = $fiyatlar['genel'];
                        $arizafiyat->ozel = $fiyatlar['ozel'];
                        $arizafiyat->genelbirim = $fiyatlar['genelbirimler'];
                        $arizafiyat->ozelbirim = $fiyatlar['ozelbirimler'];
                        $arizafiyat->ucretsiz = $ucretsiz;
                        $arizafiyat->fiyat = $fiyatlar['total'];
                        $arizafiyat->fiyat2 = $fiyatlar['total2'];
                        $arizafiyat->indirim = 0;
                        $arizafiyat->indirimorani = 0;
                        $arizafiyat->tutar = $fiyatlar['total'];
                        $arizafiyat->tutar2 = $fiyatlar['total2'];
                        $arizafiyat->kdv = $kdv;
                        $arizafiyat->kdv2 = $kdv2;
                        $arizafiyat->toplamtutar = round(($fiyatlar['total'] + $kdv)*2)/2;
                        $arizafiyat->toplamtutar2 = round(($fiyatlar['total2'] + $kdv2)*2)/2;
                        $arizafiyat->parabirimi_id = $fiyatlar['parabirimi'];
                        $arizafiyat->parabirimi2_id = $fiyatlar['parabirimi2'];
                        $arizafiyat->durum = 0;
                        $arizafiyat->kurtarihi = NULL;
                        $arizafiyat->save();
                        $arizakayit->arizakayit_durum = 1;
                        $arizakayit->save();
                        $sayacgelen->fiyatlandirma = 0;
                        $sayacgelen->musterionay = 0;
                        $sayacgelen->sayacdurum = 1;
                        $sayacgelen->teslimdurum = 0;
                        $sayacgelen->save();
                    }
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Teslim Silinemedi', 'text' =>'Sayaç Gelen ve Servis Takip Bilgisi Silinemedi', 'type' => 'error'));
                }
                try {
                    BackendController::BildirimGeriAl(4,$depoteslim->netsiscari_id,$depoteslim->servis_id,$sayacsayi);
                    BackendController::HatirlatmaGeriAl(4,$depoteslim->netsiscari_id,$depoteslim->servis_id,$sayacsayi);
                    if($depoteslim->subegonderim){
                        BackendController::HatirlatmaSil(11,$depoteslim->netsiscari_id,$depoteslim->servis_id,$sayacsayi);
                    }else if($depoteslim->tipi==3){ //Hurda
                        BackendController::HatirlatmaSil(9,$depoteslim->netsiscari_id,$depoteslim->servis_id,$sayacsayi);
                    }else{
                        BackendController::HatirlatmaSil(9,$depoteslim->netsiscari_id,$depoteslim->servis_id,$sayacsayi);
                    }
                    $depoteslim->delete();
                    BackendController::IslemEkle(3, Auth::user()->id, 'label-danger', 'fa-check', $bilgi->id . ' Numaralı Depo Teslimi Silindi.', 'Silen:' . Auth::user()->adi_soyadi . ',Depo Teslim Numarası:' . $bilgi->id);
                    DB::commit();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Teslim Silindi', 'text' => 'Depo Teslim Başarıyla Silindi.', 'type' => 'success'));
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Teslim Silinemedi', 'text' => 'Depo Teslim Silinirken Sorun Oluştu.', 'type' => 'error'));
                }
            }else{
                DB::rollBack();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Teslim Silinemedi', 'text' => 'Silinecek Depo Teslimi Bulunamadı.', 'type' => 'error'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Teslim Silinemedi', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getDepoteslimkayitsil($id){
        try {
            DB::beginTransaction();
            $sayacgelen = SayacGelen::find($id);
            $servistakip = ServisTakip::where('sayacgelen_id',$id)->first();
            $arizakayit = ArizaKayit::find($servistakip->arizakayit_id);
            $arizafiyat = ArizaFiyat::find($servistakip->arizafiyat_id);
            $degisenler = explode(',', $arizafiyat->degisenler);
            $onaylanan = Onaylanan::find($servistakip->onaylanan_id);
            $ucretlendirilen = Ucretlendirilen::find($servistakip->ucretlendirilen_id);
            $depoteslimler = DepoTeslim::where('netsiscari_id',$sayacgelen->netsiscari_id)->where('depodurum',0)->get();
            if ($depoteslimler->count()>0){
                foreach ($depoteslimler as $depoteslim){
                    $secilenler = $depoteslim->secilenler;
                    $secilenlist = explode(',', $secilenler);
                    $sayacsayi = 1;
                    if(BackendController::Listedemi($id,$secilenlist)){
                        try {
                            $bilgi = clone $depoteslim;
                            if($depoteslim->tipi==3){ //hurda
                                $hurda = Hurda::find($servistakip->hurda_id);
                                $servistakip->hurda_id = NULL;
                                $servistakip->hurdalamatarihi = NULL;
                                $servistakip->save();
                                if($hurda){
                                    $hurda->delete();
                                }
                            }
                            $servistakip->onaylanan_id = NULL;
                            $servistakip->ucretlendirilen_id = NULL;
                            $servistakip->ucretlendirmetarihi = NULL;
                            $servistakip->gondermetarihi = NULL;
                            $servistakip->onaylanmatarihi = NULL;
                            $servistakip->reddetmetarihi = NULL;
                            $servistakip->tekrarucrettarihi = NULL;
                            $servistakip->sonislemtarihi = $servistakip->arizakayittarihi;
                            $servistakip->kullanici_id = $arizakayit->arizakayit_kullanici_id;
                            $servistakip->durum = 2;
                            $servistakip->save();
                            if ($ucretlendirilen){
                                if ($ucretlendirilen->sayacsayisi > 1) {
                                    $ucretlendirilen->sayacsayisi--;
                                    $ucretlendirilen->secilenler = BackendController::getListeFark(explode(',',$ucretlendirilen->secilenler), array($arizafiyat->id));
                                    $ucretlendirilen->fiyat -= $arizafiyat->fiyat;
                                    if (!is_null($arizafiyat->parabirimi2_id)) {
                                        $ucretlendirilen->fiyat2 -= $arizafiyat->fiyat2;
                                    }
                                    $ucretlendirilen->save();
                                }else{
                                    if($onaylanan){
                                        $onaylanan->delete();
                                    }
                                    $ucretlendirilen->delete();
                                }
                            }
                            $ucretsiz = '';
                            $fiyatlar = BackendController::Fiyatlandir($degisenler, $sayacgelen->uretimyer_id);
                            $flag = 0;
                            for ($i = 0; $i < count($degisenler); $i++) {
                                $ucretsiz .= ($flag > 0 ? ',' : '') . '0';
                                $flag = 1;
                            }
                            $kdv = ($fiyatlar['total'] * 18) / 100;
                            $kdv2 = ($fiyatlar['total2'] * 18) / 100;
                            $arizafiyat->fiyatdurum = $fiyatlar['durum'];
                            $arizafiyat->degisenler = BackendController::Sort($arizafiyat->degisenler);
                            $arizafiyat->genel = $fiyatlar['genel'];
                            $arizafiyat->ozel = $fiyatlar['ozel'];
                            $arizafiyat->genelbirim = $fiyatlar['genelbirimler'];
                            $arizafiyat->ozelbirim = $fiyatlar['ozelbirimler'];
                            $arizafiyat->ucretsiz = $ucretsiz;
                            $arizafiyat->fiyat = $fiyatlar['total'];
                            $arizafiyat->fiyat2 = $fiyatlar['total2'];
                            $arizafiyat->indirim = 0;
                            $arizafiyat->indirimorani = 0;
                            $arizafiyat->tutar = $fiyatlar['total'];
                            $arizafiyat->tutar2 = $fiyatlar['total2'];
                            $arizafiyat->kdv = $kdv;
                            $arizafiyat->kdv2 = $kdv2;
                            $arizafiyat->toplamtutar = round(($fiyatlar['total'] + $kdv)*2)/2;
                            $arizafiyat->toplamtutar2 = round(($fiyatlar['total2'] + $kdv2)*2)/2;
                            $arizafiyat->parabirimi_id = $fiyatlar['parabirimi'];
                            $arizafiyat->parabirimi2_id = $fiyatlar['parabirimi2'];
                            $arizafiyat->durum = 0;
                            $arizafiyat->kurtarihi = NULL;
                            $arizafiyat->save();
                            $arizakayit->arizakayit_durum = 1;
                            $arizakayit->save();
                            $sayacgelen->fiyatlandirma = 0;
                            $sayacgelen->musterionay = 0;
                            $sayacgelen->sayacdurum = 1;
                            $sayacgelen->teslimdurum = 0;
                            $sayacgelen->save();
                        } catch (Exception $e) {
                            DB::rollBack();
                            Log::error($e);
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Teslim Silinemedi', 'text' =>'Sayaç Gelen ve Servis Takip Bilgisi Silinemedi', 'type' => 'error'));
                        }
                        try {
                            BackendController::BildirimGeriAl(4,$depoteslim->netsiscari_id,$depoteslim->servis_id,$sayacsayi);
                            BackendController::HatirlatmaGeriAl(4,$depoteslim->netsiscari_id,$depoteslim->servis_id,$sayacsayi);
                            if($depoteslim->subegonderim){
                                BackendController::HatirlatmaSil(11,$depoteslim->netsiscari_id,$depoteslim->servis_id,$sayacsayi);
                            }else if($depoteslim->tipi==3){ //Hurda
                                BackendController::HatirlatmaSil(9,$depoteslim->netsiscari_id,$depoteslim->servis_id,$sayacsayi);
                            }else{
                                BackendController::HatirlatmaSil(9,$depoteslim->netsiscari_id,$depoteslim->servis_id,$sayacsayi);
                            }
                            if($depoteslim->sayacsayisi>1){
                                $depoteslim->sayacsayisi--;
                                $depoteslim->secilenler = BackendController::getListeFark($secilenlist, array($id));
                                $depoteslim->save();
                            }else{
                                $depoteslim->delete();
                            }
                            BackendController::IslemEkle(3, Auth::user()->id, 'label-danger', 'fa-check', $bilgi->id . ' Numaralı Depo Teslimi Silindi.', 'Silen:' . Auth::user()->adi_soyadi . ',Depo Teslim Numarası:' . $bilgi->id);
                            DB::commit();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Teslim Silindi', 'text' => 'Depo Teslim Başarıyla Silindi.', 'type' => 'success'));
                        } catch (Exception $e) {
                            DB::rollBack();
                            Log::error($e);
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Teslim Silinemedi', 'text' => 'Depo Teslim Silinirken Sorun Oluştu.', 'type' => 'error'));
                        }
                    }
                }
            }else{
                DB::rollBack();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Teslim Silinemedi', 'text' => 'Silinecek Depo Teslimi Bulunamadı.', 'type' => 'error'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Teslim Silinemedi', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getTeslimatbilgi() {
        try {
            $id=Input::get('id');
            $depoteslim = DepoTeslim::find($id);
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
            $teslimadres = DepoTeslim::selectRaw('count(*) AS sayi, teslimadres')->where('netsiscari_id',$depoteslim->netsiscari_id)->where('depodurum',1)->where('teslimadres','<>','')->groupBy('teslimadres')->orderBy('sayi', 'DESC')->get();
            return Response::json(array('durum'=>true,'depoteslim' => $depoteslim,'teslimadres' => $teslimadres ));
        } catch (Exception $e) {
            return Response::json(array('durum' => false, 'title' => 'Depo Teslimat Bilgisinde Hata', 'text' => str_replace("'","\'",$e->getMessage())));
        }
    }

    public function getTeslimatucretlibilgi() {
        try {
            $id=Input::get('id');
            $depoteslim = DepoTeslim::find($id);
            $depoteslim->netsiscari = Netsiscari::find($depoteslim->netsiscari_id);
            $depoteslim->servis = Servis::find($depoteslim->servis_id);
            $depoteslim->yetkili = ServisYetkili::where('kullanici_id', Auth::user()->id)->first();
            if (!$depoteslim->yetkili)
                return Response::json(array('durum' => false,'title'=>'Kullanıcı Servis Hatası','text'=>'Kullanici Servis birimine kayıtlı değil','type'=>'error'));
            if ($depoteslim->teslimtarihi)
                $depoteslim->teslimtarihi = date('d-m-Y', strtotime($depoteslim->teslimtarihi));
            else
                $depoteslim->teslimtarihi = "";
            $secilenler = explode(',', $depoteslim->secilenler);
            $depoteslim->arizafiyat = ArizaFiyat::whereIn('sayacgelen_id', $secilenler)->get();
            $depoteslim->parabirimi = ParaBirimi::find($depoteslim->parabirimi_id);
            $depoteslim->parabirimi2 = ParaBirimi::find($depoteslim->parabirimi2_id);
            if ($depoteslim->arizafiyat->count() > 0) {
                foreach ($depoteslim->arizafiyat as $arizafiyat) {
                    $arizafiyat->sayacadi = SayacAdi::find($arizafiyat->sayacadi_id);
                    $arizafiyat->sayaccap = SayacCap::find($arizafiyat->sayaccap_id);
                    $arizafiyat->uretimyer = UretimYer::find($arizafiyat->uretimyer_id);
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
            } else {
                $depoteslim->sayacgelen = SayacGelen::whereIn('id', $secilenler)->get();
                foreach ($depoteslim->sayacgelen as $sayacgelen) {
                    $sayacgelen->sayacadi = SayacAdi::find($sayacgelen->sayacadi_id);
                    $sayacgelen->sayaccap = SayacCap::find($sayacgelen->sayaccap_id);
                    $sayacgelen->uretimyer = UretimYer::find($sayacgelen->uretimyer_id);
                }
            }
            $teslimadres = DepoTeslim::selectRaw('count(*) AS sayi, teslimadres')->where('netsiscari_id',$depoteslim->netsiscari_id)->where('depodurum',1)->where('teslimadres','<>','')->groupBy('teslimadres')->orderBy('sayi', 'DESC')->get();
            return Response::json(array('durum'=>true,'depoteslim' => $depoteslim,'teslimadres' => $teslimadres ));
        } catch (Exception $e) {
            return Response::json(array('durum' => false, 'title' => 'Depo Teslimat Bilgisinde Hata', 'text' => str_replace("'","\'",$e->getMessage()),'type'=>'error'));
        }
    }

    public function postTeslimet($id){
        try{
            if (Input::has('mailvar')) //mail gönderilecek
            {
                $rules = ['yetkilimail' => 'required|email'];
                $mail = 1;
                $validate = Validator::make(Input::all(), $rules);
                $messages = $validate->messages();
                if ($validate->fails()) {
                    Input::flash();
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
                }
            } else // mail gönderilmeyecek
            {
                $mail = 0;
            }
            $digermail = Input::get('mailcc');
            $digermaillist = explode(';', $digermail);
            if ($digermail != "")
                foreach ($digermaillist as $email) {
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Diğer Mail Listesi Hatası', 'text' => $email . ' geçerli bir email değil. Birden fazla girilen mail adreslerini ; ile ayırınız!', 'type' => 'error'));
                    }
                }
            $yetkilimail = Input::get('yetkilimail');
            $depoteslim=DepoTeslim::find($id);
            if(Input::has('faturavar')) //fatura basılacak
                $fatura=1;
            else // fatura basılmayacak
                $fatura=0;
            switch(Input::has('teslimaciklama1') ? Input::get('teslimaciklama1') : $depoteslim->tipi){
                case 0: $belge1='TAMİR BAKIM';break;
                case 1: $belge1='GARANTİ KAPSAMINDA YAPILMIŞTIR.FATURA EDİLMEYECEKTİR.';break;
                case 2: $belge1='GERİ GÖNDERİMDİR. FATURA EDİLMEYECEKTİR.';break;
                case 3: $belge1='HURDA SAYAÇTIR. FATURA EDİLMEYECEKTİR.';break;
                case 4: $belge1='DEPOLAR ARASI SEVKTİR. FATURA EDİLMEYECEKTİR.';break;
                //case 5: $belge1='ŞİKAYETLİ MUAYENE KAPSAMINDA DEĞERLENDİRİLMİŞTİR. FATURA EDİLMEYECEKTİR.';break;
                default: $belge1='TAMİR BAKIM';
            }
            $belge2=Input::get('teslimaciklama2');
            $belge3=Input::get('teslimaciklama3');
            $faturasizaciklama=Input::get('teslimaciklama4');
            $adres=Input::get('teslimadres');
            DB::beginTransaction();
            $secilenler = Input::get('teslimsecilenler');
            $secilenlist=explode(',',$secilenler);
            $tumu=explode(',',Input::get('teslimtumu'));
            $birim = Input::get('teslimbirim');
            $birim2 = Input::get('teslimbirim2')=="" ? null : Input::get('teslimbirim2');
            $sayacsayisi=count($secilenlist);
            $servisid=$depoteslim->servis_id;
            $netsiscariid=$depoteslim->netsiscari_id;
            $kullanici=Kullanici::find(Auth::user()->id);
            if(SayacGelen::whereIn('id',$secilenlist)->where('depoteslim',1)->get()->count()>0){
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Teslimi Tamamlanamadı!', 'text' => 'Seçilen Depo Teslimatındaki Sayaçların Çıkışı Zaten Yapılmış!', 'type' => 'warning'));
            }
            $arizafiyatlar=ArizaFiyat::whereIn('sayacgelen_id',$secilenlist)->get();
            $yetkili = ServisYetkili::where('kullanici_id', $kullanici->id)->first();
            $netsiscari= NetsisCari::find($depoteslim->netsiscari_id);
            if($netsiscari->caridurum!="A")
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Netsis Cari Uyarısı', 'text' => 'Cari Bilgisi Netsis Üzerinde Kilitli.', 'type' => 'warning'));
            if($arizafiyatlar->count()!=count($secilenlist))
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Sayısı Uyarısı', 'text' => 'Arıza Kayıdı Yapılan Sayaç Sayısı ile Teslim Edilen Sayaç Sayısı Farklı.Aynı Seri Numarası için birden fazla arıza kayıdı olabilir.', 'type' => 'warning'));
            $servis=Servis::find($depoteslim->servis_id);
            $netsiscari->depoyetkili = $yetkilimail;
            $netsiscari->save();
            DB::commit();
            DB::beginTransaction();
            foreach ($arizafiyatlar as $arizafiyat) {
                $sayacgelen = SayacGelen::find($arizafiyat->sayacgelen_id);
                if ($sayacgelen->depoteslim) {
                    DB::rollBack();
                    return Redirect::to($this->servisadi . '/arizakayit')->with(array('mesaj' => 'true', 'title' => 'Depo Teslimatı Yapılamadı', 'text' => $sayacgelen->serino . ' Nolu Sayaç Zaten Teslim Edilmiş!', 'type' => 'error'));
                }
            }
            if(count($secilenlist)==count($tumu)) //depo teslimdeki tum sayaçlar seçilmiş
            {
                if ($mail) {
                    $sayacliste = BackendController::getTeslimListesi($id);
                    if (is_null($sayacliste)) {
                        DB::rollBack();
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Gönderme Hatası', 'text' => 'Sayaç Listesi Oluşturulamadı', 'type' => 'error'));
                    }
                }
                try{
                    $depoteslim->depodurum=1;
                    $depoteslim->kullanici_id=$kullanici->id;
                    $depoteslim->teslimtarihi=date('Y-m-d H:i:s');
                    $depoteslim->carikod=$netsiscari->carikod;
                    switch($depoteslim->tipi){
                        case 0 : $depoteslim->ozelkod='V';
                            break;
                        case 1 : $depoteslim->ozelkod='W';
                            break;
                        case 2 : $depoteslim->ozelkod='0';
                            break;
                        case 3 : $depoteslim->ozelkod='X';
                            break;
                        case 4 : $depoteslim->ozelkod='0';
                            break;
                        //case 5 : $depoteslim->ozelkod='0';
                        //    break;
                        default: $depoteslim->ozelkod='V';
                            break;
                    }
                    $depoteslim->plasiyerkod=$yetkili->plasiyerkod;
                    $depoteslim->faturaadres=$netsiscari->adres.' '.$netsiscari->il.' '.$netsiscari->ilce;
                    $depoteslim->teslimadres=$adres;
                    $depoteslim->depokodu=$servis->depokodu;
                    $depoteslim->aciklama=$servis->servisadi;
                    $depoteslim->belge1=$belge1;
                    $depoteslim->belge2=$belge2;
                    $depoteslim->belge3=$belge3;
                    $depoteslim->netsiskullanici=$yetkili->netsiskullanici;
                    $depoteslim->save();
                }catch (Exception $e){
                    Log::error($e);
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Teslimatı yapılamadı', 'text' => 'Depo Teslimatı yapılırken hata oluştu', 'type' => 'error'));
                }

                if($fatura==0) //fatura basılmayacaksa
                {
                    $durum=0;
                    $depoteslim->belge1 = $faturasizaciklama!="" ? $faturasizaciklama : "";
                    $depoteslim->save();
                }else {
                    $durum = BackendController::NetsisFatura($id, 0);
                }
                if($durum==0 || $durum==1)
                {
                    try{
                        foreach ($arizafiyatlar as $arizafiyat) {
                            $degisenler=explode(',',$arizafiyat->degisenler);
                            if (BackendController::getStokKodDurum()) // stok durumu aktifse
                            {
                                $stokdurumlari = StokDurum::whereIn('degisenler_id', $degisenler)->get();
                                foreach ($stokdurumlari as $stokdurum) {
                                    $stokdurum->kalan -= ($stokdurum->adet);
                                    $stokdurum->kullanilan -= ($stokdurum->adet);
                                    $stokdurum->biten += ($stokdurum->adet);
                                    $stokdurum->save();
                                }
                            }
                            $sayacgelen=SayacGelen::find($arizafiyat->sayacgelen_id);
                            $sayacgelen->depoteslim=1;
                            $sayacgelen->save();
                            $servistakip=ServisTakip::where('arizafiyat_id',$arizafiyat->id)->first();
                            $servistakip->depoteslim_id=$depoteslim->id;
                            $servistakip->durum=9;
                            $servistakip->depoteslimtarihi=$depoteslim->teslimtarihi;
                            $servistakip->kullanici_id=Auth::user()->id;
                            $servistakip->sonislemtarihi=$depoteslim->teslimtarihi;
                            $servistakip->save();
                            $sayac=Sayac::find($arizafiyat->sayac_id);
                            $sayac->songelistarihi=$sayacgelen->depotarihi;
                            $sayac->save();
                        }
                        try {
                            if ($mail) {
                                try {
                                    Mail::send('mail.teslimbilgilendirme', array('depoteslim' => $depoteslim),
                                        function ($message) use ($netsiscari, $yetkilimail, $digermaillist, $digermail) {
                                            if ($digermail != "")
                                                foreach ($digermaillist as $email)
                                                    $message->cc($email);
                                            $message->to($yetkilimail, $yetkilimail)->subject("Manas ServisTakip Bilgilendirme");
                                            $message->attach(public_path('reports/outputs/sayaclistesi/'. mb_substr("SayacListesi_" . Str::slug($netsiscari->cariadi),0,20) . '.pdf'));
                                        });
                                } catch (Exception $e) {
                                    DB::rollBack();
                                    Log::error($e);
                                    File::delete(public_path('reports/outputs/sayaclistesi/'. mb_substr("SayacListesi_" . Str::slug($netsiscari->cariadi),0,20) . '.pdf'));
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Hatası', 'text' => 'Mail Gönderirken Hata Oluştu', 'type' => 'error'));
                                }
                                if (count(Mail::failures()) > 0) {
                                    DB::rollBack();
                                    File::delete(public_path('reports/outputs/sayaclistesi/'. mb_substr("SayacListesi_" . Str::slug($netsiscari->cariadi),0,20) . '.pdf'));
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Hatası', 'text' => 'Mail Gönderirken Hata Oluştu', 'type' => 'error'));
                                } else {
                                    File::delete(public_path('reports/outputs/sayaclistesi/'. mb_substr("SayacListesi_" . Str::slug($netsiscari->cariadi),0,20) . '.pdf'));
                                }
                            } else {
                                File::delete(public_path('reports/outputs/sayaclistesi/'. mb_substr("SayacListesi_" . Str::slug($netsiscari->cariadi),0,20) . '.pdf'));
                            }
                        } catch (Exception $e) {
                            DB::rollBack();
                            Log::error($e);
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Hatası', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
                        }
                    }catch (Exception $e){
                        Log::error($e);
                        DB::rollBack();
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Teslimatı Yapılırken Hata Oluştu', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
                    }
                    BackendController::HatirlatmaGuncelle(9,$netsiscariid,$servisid,$sayacsayisi);
                    BackendController::BildirimEkle(9,$netsiscariid,$servisid,$sayacsayisi);
                    BackendController::IslemEkle(1,Auth::user()->id,'label-success','fa-truck',$depoteslim->id.' Nolu Depo Teslimatı Yapıldı.','Teslim Eden:'.$kullanici->adi_soyadi.',Depo Teslim Numarası:'.$depoteslim->id);
                    DB::commit();
                    if($durum==0)
                        return Redirect::to('depo/depoteslim')->with(array('mesaj' => 'true', 'title' => 'Depo Teslimatı Başarıyla Yapıldı', 'text' => 'Faturası kesilmeden gönderildi', 'type' => 'success'));
                    else
                        return Redirect::to('depo/depoteslim')->with(array('mesaj' => 'true', 'title' => 'Depo Teslimatı Başarıyla Yapıldı', 'text' => 'Faturası kesilerek depo teslimatı gerçekleşti', 'type' => 'success'));
                }else{
                    DB::rollBack();
                    if($durum==-1){
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Fatura Netsise kaydedilemedi', 'type' => 'error'));
                    }else if($durum==2){
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Fatura Açıklaması Kaydedilemedi', 'type' => 'error'));
                    }else if($durum==3){
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Fatura Genel Bilgileri Kaydedilemedi', 'type' => 'error'));
                    }else if($durum==4){
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Fatura Kalemleri Kaydedilemedi', 'type' => 'error'));
                    }else if($durum==5){
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Bugünün Döviz Kuru Netsisten alınamadı', 'type' => 'error'));
                    }else if($durum==6){
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Son Fatura Bilgisi Bulunamadı', 'type' => 'error'));
                    }else if($durum==7){
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Bu Fatura Numarası Sistemde Kayıtlı', 'type' => 'error'));
                    }else{
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => str_replace("'","\'",$durum), 'type' => 'error'));
                    }
                }
            }else{ //bazıları seçilmiş
                try {
                    $yenidepoteslim = new DepoTeslim;
                    $yenidepoteslim->servis_id = $servisid;
                    $yenidepoteslim->netsiscari_id = $netsiscariid;
                    $yenidepoteslim->secilenler = $secilenler;
                    $yenidepoteslim->sayacsayisi = count($secilenlist);
                    $yenidepoteslim->depodurum = 1;
                    $yenidepoteslim->tipi = $depoteslim->tipi;
                    $yenidepoteslim->periyodik = $depoteslim->periyodik;
                    $yenidepoteslim->subegonderim = $depoteslim->subegonderim;
                    $yenidepoteslim->parabirimi_id=$birim;
                    $yenidepoteslim->parabirimi2_id=$birim2;
                    $yenidepoteslim->kullanici_id = $kullanici->id;
                    $yenidepoteslim->teslimtarihi = date('Y-m-d H:i:s');
                    $yenidepoteslim->carikod = $netsiscari->carikod;
                    switch($depoteslim->tipi){
                        case 0 : $yenidepoteslim->ozelkod='V';
                            break;
                        case 1 : $yenidepoteslim->ozelkod='W';
                            break;
                        case 2 : $yenidepoteslim->ozelkod='0';
                            break;
                        case 3 : $yenidepoteslim->ozelkod='X';
                            break;
                        case 4 : $yenidepoteslim->ozelkod='0';
                            break;
                        //case 5 : $yenidepoteslim->ozelkod='0';
                        //    break;
                        default: $yenidepoteslim->ozelkod='V';
                            break;
                    }
                    $yenidepoteslim->plasiyerkod = $yetkili->plasiyerkod;
                    $yenidepoteslim->faturaadres = $netsiscari->adres . ' ' . $netsiscari->il . ' ' . $netsiscari->ilce;
                    $yenidepoteslim->teslimadres = $adres;
                    $yenidepoteslim->depokodu = $servis->depokodu;
                    $yenidepoteslim->aciklama = $servis->servisadi;
                    $yenidepoteslim->belge1 = $belge1;
                    $yenidepoteslim->belge2 = $belge2;
                    $yenidepoteslim->belge3=$belge3;
                    $yenidepoteslim->netsiskullanici = $yetkili->netsiskullanici;
                    $yenidepoteslim->save();
                    DB::commit();
                    if ($mail) {
                        $sayacliste = BackendController::getTeslimListesi($yenidepoteslim->id);
                        if (is_null($sayacliste)) {
                            $yenidepoteslim->delete();
                            DB::commit();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Gönderme Hatası', 'text' => 'Sayaç Listesi Oluşturulamadı', 'type' => 'error'));
                        }
                    }
                    $kalanlar = BackendController::getListeFark($tumu, $secilenlist);
                    $kalanlist = explode(',', $kalanlar);
                    $kalansayi = count($kalanlist);
                    $kalanparabirimi = $depoteslim->parabirimi_id;
                    $kalanparabirimi2 = null;
                    foreach ($kalanlist as $sayacgelenid) {
                        $arizafiyat = ArizaFiyat::where('sayacgelen_id',$sayacgelenid)->first();
                        if ($kalanparabirimi2 == null) {
                            if ($arizafiyat->parabirimi2_id != null) {
                                $kalanparabirimi2 = $arizafiyat->parabirimi2_id;
                            }
                        } else {
                            if ($arizafiyat->parabirimi2_id != null) {
                                if ($arizafiyat->parabirimi2_id != $kalanparabirimi2) {
                                    DB::rollBack();
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Teslimi Yapılamadı!', 'text' => 'Kalan Sayaçlara ait Ücretlendirmede İki Parabiriminden Fazla Kullanımış!', 'type' => 'error'));
                                }
                            }
                        }
                    }
                    $depoteslim->secilenler = $kalanlar;
                    $depoteslim->sayacsayisi = $kalansayi;
                    $depoteslim->parabirimi_id = $kalanparabirimi;
                    $depoteslim->parabirimi2_id = $kalanparabirimi2;
                    $depoteslim->save();
                }catch (Exception $e) {
                    Log::error($e);
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Teslimatı yapılamadı', 'text' => 'Yeni Depo Teslimatı yapılırken hata oluştu', 'type' => 'error'));
                }

                if($fatura==0) //fatura basılmayacaksa
                {
                    $durum=0;
                }else {
                    $durum = BackendController::NetsisFatura($yenidepoteslim->id, 0);
                }
                if($durum==0 || $durum==1)
                {
                    try{
                        foreach ($arizafiyatlar as $arizafiyat) {
                            $degisenler=explode(',',$arizafiyat->degisenler);
                            if (BackendController::getStokKodDurum()) // stok durumu aktifse
                            {
                                $stokdurumlari = StokDurum::whereIn('degisenler_id', $degisenler)->get();
                                foreach ($stokdurumlari as $stokdurum) {
                                    $stokdurum->kalan -= ($stokdurum->adet);
                                    $stokdurum->kullanilan -= ($stokdurum->adet);
                                    $stokdurum->biten += ($stokdurum->adet);
                                    $stokdurum->save();
                                }
                            }
                            $sayacgelen=SayacGelen::find($arizafiyat->sayacgelen_id);
                            $sayacgelen->depoteslim=1;
                            $sayacgelen->save();
                            $servistakip=ServisTakip::where('arizafiyat_id',$arizafiyat->id)->first();
                            $servistakip->depoteslim_id=$yenidepoteslim->id;
                            $servistakip->durum=9;
                            $servistakip->depoteslimtarihi=$yenidepoteslim->teslimtarihi;
                            $servistakip->kullanici_id=Auth::user()->id;
                            $servistakip->sonislemtarihi=$yenidepoteslim->teslimtarihi;
                            $servistakip->save();
                            $sayac=Sayac::find($arizafiyat->sayac_id);
                            $sayac->songelistarihi=$sayacgelen->depotarihi;
                            $sayac->save();
                        }
                        try {
                            if ($mail) {
                                try {
                                    Mail::send('mail.teslimbilgilendirme', array('depoteslim' => $yenidepoteslim),
                                        function ($message) use ($netsiscari, $yetkilimail, $digermaillist, $digermail) {
                                            if ($digermail != "")
                                                foreach ($digermaillist as $email)
                                                    $message->cc($email);
                                            $message->to($yetkilimail, $yetkilimail)->subject("Manas ServisTakip Bilgilendirme");
                                            $message->attach(public_path('reports/outputs/sayaclistesi/'. mb_substr("SayacListesi_" . Str::slug($netsiscari->cariadi),0,20) . '.pdf'));
                                        });
                                } catch (Exception $e) {
                                    DB::rollBack();
                                    Log::error($e);
                                    File::delete(public_path('reports/outputs/sayaclistesi/'. mb_substr("SayacListesi_" . Str::slug($netsiscari->cariadi),0,20) . '.pdf'));
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Hatası', 'text' => 'Mail Gönderirken Hata Oluştu', 'type' => 'error'));
                                }
                                if (count(Mail::failures()) > 0) {
                                    DB::rollBack();
                                    File::delete(public_path('reports/outputs/sayaclistesi/'. mb_substr("SayacListesi_" . Str::slug($netsiscari->cariadi),0,20) . '.pdf'));
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Hatası', 'text' => 'Mail Gönderirken Hata Oluştu', 'type' => 'error'));
                                } else {
                                    File::delete(public_path('reports/outputs/sayaclistesi/'. mb_substr("SayacListesi_" . Str::slug($netsiscari->cariadi),0,20) . '.pdf'));
                                }
                            } else {
                                File::delete(public_path('reports/outputs/sayaclistesi/'. mb_substr("SayacListesi_" . Str::slug($netsiscari->cariadi),0,20) . '.pdf'));
                            }
                        } catch (Exception $e) {
                            DB::rollBack();
                            Log::error($e);
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Mail Hatası', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
                        }
                    }catch (Exception $e){
                        Log::error($e);
                        DB::rollBack();
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Teslimatı Yapılırken Hata Oluştu', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
                    }
                    BackendController::HatirlatmaGuncelle(9,$netsiscariid,$servisid,$sayacsayisi);
                    BackendController::BildirimEkle(9,$netsiscariid,$servisid,$sayacsayisi);
                    BackendController::IslemEkle(1,Auth::user()->id,'label-success','fa-truck',$yenidepoteslim->id.' Nolu Depo Teslimatı Yapıldı.','Teslim Eden:'.$kullanici->adi_soyadi.',Depo Teslim Numarası:'.$yenidepoteslim->id);
                    DB::commit();
                    if($durum==0)
                        return Redirect::to('depo/depoteslim')->with(array('mesaj' => 'true', 'title' => 'Depo Teslimatı Başarıyla Yapıldı', 'text' => 'Faturası kesilmeden gönderildi', 'type' => 'success'));
                    else
                        return Redirect::to('depo/depoteslim')->with(array('mesaj' => 'true', 'title' => 'Depo Teslimatı Başarıyla Yapıldı', 'text' => 'Faturası kesilerek depo teslimatı gerçekleşti', 'type' => 'success'));
                }else{
                    DB::rollBack();
                    if($durum==-1){
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Fatura Netsise kaydedilemedi', 'type' => 'error'));
                    }else if($durum==2){
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Fatura Açıklaması Kaydedilemedi', 'type' => 'error'));
                    }else if($durum==3){
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Fatura Genel Bilgileri Kaydedilemedi', 'type' => 'error'));
                    }else if($durum==4){
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Fatura Kalemleri Kaydedilemedi', 'type' => 'error'));
                    }else if($durum==5){
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Bugünün Döviz Kuru Netsisten alınamadı', 'type' => 'error'));
                    }else if($durum==6){
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Son Fatura Bilgisi Bulunamadı', 'type' => 'error'));
                    }else if($durum==7){
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Bu Fatura Numarası Sistemde Kayıtlı', 'type' => 'error'));
                    }else{
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => str_replace("'","\'",$durum), 'type' => 'error'));
                    }
                }
            }
        }catch (Exception $e){
            Log::error($e);
            DB::rollBack();
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Teslimatı yapılamadı', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function postDepolararasigonder($id){
        try{ //TODO düzenlenecek
            DB::beginTransaction();
            if(Input::has('depolararasifaturavar')) //fatura basılacak
                $fatura=1;
            else // fatura basılmayacak
                $fatura=0;
            switch(Input::get('depolararasiaciklama1')){
                case 0: $belge1='DEPOLAR ARASI SEVKTİR. FATURA EDİLMEYECEKTİR.';break;
                case 1: $belge1='HURDA SAYAÇTIR. FATURA EDİLMEYECEKTİR.';break;
                case 2: $belge1='GERİ GÖNDERİMDİR. FATURA EDİLMEYECEKTİR.';break;
                default: $belge1='';
            }
            $belge2=Input::get('depolararasiaciklama2');
            $adres=Input::get('teslimadres');
            $secilenler = Input::get('depolararasisecilenler');
            $secilenlist=explode(',',$secilenler);
            $tumu=explode(',',Input::get('depolararasitumu'));
            $sayacsayisi=count($secilenlist);
            $depoteslim=DepoTeslim::find($id);
            $servisid=$depoteslim->servis_id;
            $netsiscariid=$depoteslim->netsiscari_id;
            $kullanici=Kullanici::find(Auth::user()->id);
            if(SayacGelen::whereIn('id',$secilenlist)->where('depoteslim',1)->get()->count()>0){
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Teslimi Tamamlanamadı!', 'text' => 'Seçilen Depo Teslimatındaki Sayaçların Çıkışı Zaten Yapılmış!', 'type' => 'warning'));
            }
            $arizafiyatlar=ArizaFiyat::whereIn('sayacgelen_id',$secilenlist)->get();
            foreach ($arizafiyatlar as $arizafiyat) {
                $sayacgelen = SayacGelen::find($arizafiyat->sayacgelen_id);
                if ($sayacgelen->depoteslim) {
                    DB::rollBack();
                    return Redirect::to($this->servisadi . '/arizakayit')->with(array('mesaj' => 'true', 'title' => 'Depo Teslimatı Yapılamadı', 'text' => $sayacgelen->serino . ' Nolu Sayaç Zaten Teslim Edilmiş!', 'type' => 'error'));
                }
            }
            $yetkili = ServisYetkili::where('kullanici_id', $kullanici->id)->first();
            $netsiscari= NetsisCari::find($depoteslim->netsiscari_id);
            if($netsiscari->caridurum!="A")
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Netsis Cari Uyarısı', 'text' => 'Cari Bilgisi Netsis Üzerinde Kilitli.', 'type' => 'warning'));
            $servis=Servis::find($depoteslim->servis_id);
            if(count($secilenlist)==count($tumu)) //depo teslimdeki tum sayaçlar seçilmiş
            {
                try{
                    $depoteslim->depodurum=1;
                    $depoteslim->kullanici_id=$kullanici->id;
                    $depoteslim->teslimtarihi=date('Y-m-d H:i:s');
                    $depoteslim->carikod=$netsiscari->carikod;
                    $depoteslim->ozelkod='0';
                    $depoteslim->plasiyerkod=$yetkili->plasiyerkod;
                    $depoteslim->faturaadres=$netsiscari->adres.' '.$netsiscari->il.' '.$netsiscari->ilce;
                    $depoteslim->teslimadres=$adres;
                    $depoteslim->depokodu=$servis->depokodu;
                    $depoteslim->aciklama=$servis->servisadi;
                    $depoteslim->belge1=$belge1;
                    $depoteslim->belge2=$belge2;
                    $depoteslim->netsiskullanici=$yetkili->netsiskullanici;
                    $depoteslim->save();
                }catch (Exception $e){
                    Log::error($e);
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Teslimatı yapılamadı', 'text' => 'Depo Teslimatı yapılırken hata oluştu', 'type' => 'error'));
                }
                if($fatura==0) //fatura basılmayacaksa
                {
                    $durum=0;
                }else {
                    $durum = BackendController::NetsisDepolararasi($id);
                }
                if($durum==0 || $durum==1)
                {
                    try{
                        foreach ($arizafiyatlar as $arizafiyat) {
                            $degisenler=explode(',',$arizafiyat->degisenler);
                            if (BackendController::getStokKodDurum()) // stok durumu aktifse
                            {
                                $stokdurumlari = StokDurum::whereIn('degisenler_id', $degisenler)->get();
                                foreach ($stokdurumlari as $stokdurum) {
                                    $stokdurum->kalan -= ($stokdurum->adet);
                                    $stokdurum->kullanilan -= ($stokdurum->adet);
                                    $stokdurum->biten += ($stokdurum->adet);
                                    $stokdurum->save();
                                }
                            }
                            $sayacgelen=SayacGelen::find($arizafiyat->sayacgelen_id);
                            $sayacgelen->depoteslim=1;
                            $sayacgelen->teslimdurum = 4; //depolararası sevk burdan sonra şubede gözükecek //0 bekliyor 1 teslimat 2 geri gönderim 3 hurda 4 depolararasi 5 periyodik bakım
                            $sayacgelen->save();
                            $servistakip=ServisTakip::where('arizafiyat_id',$arizafiyat->id)->first();
                            if($durum){
                                $arizafiyat->durum = 1;
                                $arizafiyat->save();
                                if($servistakip->subetakip_id) {
                                    $subeservistakip = ServisTakip::find($servistakip->subetakip_id);
                                    if ($depoteslim->tipi==3) { //abone teslimine hurda sayaç eklenecek
                                        // abone yoksa onaylama izin verme
                                        $abonesayac = AboneSayac::where('serino', $sayacgelen->serino)->first();
                                        if ($abonesayac) { //abonesayacı mevcut
                                            $abonetahsis = AboneTahsis::where('abonesayac_id', $abonesayac->id)->first();
                                            if ($abonetahsis) {//sayaç aboneye tahsisliyse
                                                try {
                                                    $abone = Abone::find($abonetahsis->abone_id);
                                                    $abonesayacgelen = SayacGelen::where('id', $subeservistakip->sayacgelen_id)->where('serino', $abonesayac->serino)->first();
                                                    try {
                                                        $aboneteslim = AboneTeslim::where('abone_id', $abone->id)->where('teslimdurum', 0)->first();
                                                        if ($aboneteslim) { //teslim edilmemiş o kişiye ait sayaç varsa
                                                            $aboneteslim->secilenler .= ',' . $abonesayacgelen->id;
                                                            $aboneteslim->sayacsayisi += 1;
                                                            $aboneteslim->save();
                                                        } else { // yeni abone teslimi
                                                            $aboneteslim = new AboneTeslim;
                                                            $aboneteslim->abone_id = $abone->id;
                                                            $aboneteslim->uretimyer_id = $abone->uretimyer_id;
                                                            $aboneteslim->netsiscari_id = $abone->netsiscari_id;
                                                            $aboneteslim->secilenler = $abonesayacgelen->id;
                                                            $aboneteslim->sayacsayisi = 1;
                                                            $aboneteslim->teslimdurum = 0;
                                                            $aboneteslim->subekodu = $abone->subekodu;
                                                            $aboneteslim->save();
                                                        }
                                                        BackendController::HurdaKayitEkle($servistakip->id); //depoteslimi hurdakayıdı ya da depocikis olacak
                                                    } catch (Exception $e) {
                                                        Log::error($e);
                                                        DB::rollBack();
                                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Teslimatı Yapılamadı', 'text' => 'Teslim Edilen sayaçlar abone teslimine kaydedilemedi!', 'type' => 'error'));
                                                    }
                                                } catch (Exception $e) {
                                                    Log::error($e);
                                                    DB::rollBack();
                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Teslimatı Yapılamadı', 'text' => 'Teslim Edilen sayaçlardan bir veya birkaçı abone teslimine kaydedilemedi!', 'type' => 'error'));
                                                }
                                            } else {//tahsisli olmayan sayaç mevcut
                                                DB::rollBack();
                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Teslimatı Yapılamadı', 'text' => 'Teslim Edilen sayaçlardan bir veya birkaçı aboneye tahsisli değil. Önce tahsis işlemlerinin tamamlanması gerekiyor!', 'type' => 'error'));
                                            }
                                        } else {//listede olmayan sayaç mevcut
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Teslimatı Yapılamadı', 'text' => 'Teslim Edilen sayaçlardan bir veya birkaçı aboneye tahsisli değil. Önce tahsis işlemlerinin tamamlanması gerekiyor!', 'type' => 'error'));
                                        }
                                    } else {
                                        BackendController::DepoTeslimEkle($servistakip->id); //depoteslimi hurdakayıdı ya da depocikis olacak
                                    }
                                }else{
                                    DB::rollBack();
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Teslimatı Yapılamadı', 'text' => 'Teslim Edilen Sayaçların Şubedeki Kayıdı Bulunamadı!', 'type' => 'error'));
                                }
                            }else{ //hurda sayaç gönderilmeden çıkış yapılır
                                BackendController::DepoCikisEkle($servistakip->id); //depoteslimi hurdakayıdı ya da depocikis olacak
                            }
                            $servistakip->depoteslim_id=$depoteslim->id;
                            $servistakip->durum=9;
                            $servistakip->depoteslimtarihi=$depoteslim->teslimtarihi;
                            $servistakip->kullanici_id=Auth::user()->id;
                            $servistakip->sonislemtarihi=$depoteslim->teslimtarihi;
                            $servistakip->save();
                            $sayac=Sayac::find($arizafiyat->sayac_id);
                            $sayac->songelistarihi=$sayacgelen->depotarihi;
                            $sayac->save();
                        }
                    }catch (Exception $e){
                        Log::error($e);
                        DB::rollBack();
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Teslimatı Yapılırken Hata Oluştu', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
                    }
                    BackendController::HatirlatmaGuncelle(9,$netsiscariid,$servisid,$sayacsayisi);
                    BackendController::BildirimEkle(9,$netsiscariid,$servisid,$sayacsayisi);
                    if($durum){
                        if($depoteslim->tipi==3)
                            BackendController::HatirlatmaEkle(12,$netsiscariid,6,$sayacsayisi);
                        else
                            BackendController::HatirlatmaEkle(4,$netsiscariid,6,$sayacsayisi);
                    }else{
                        if($depoteslim->tipi==3)
                            BackendController::BildirimEkle(10,$netsiscariid,6,$sayacsayisi);
                        else
                            BackendController::BildirimEkle(9,$netsiscariid,6,$sayacsayisi);
                    }
                    BackendController::IslemEkle(1,Auth::user()->id,'label-success','fa-truck',$depoteslim->id.' Nolu Depo Teslimatı Yapıldı.','Teslim Eden:'.$kullanici->adi_soyadi.',Depo Teslim Numarası:'.$depoteslim->id);
                    DB::commit();
                    if($durum==0)
                        return Redirect::to('depo/depoteslim')->with(array('mesaj' => 'true', 'title' => 'Depo Teslimatı Başarıyla Yapıldı', 'text' => 'Faturası kesilmeden gönderildi', 'type' => 'success'));
                    else
                        return Redirect::to('depo/depoteslim')->with(array('mesaj' => 'true', 'title' => 'Depo Teslimatı Başarıyla Yapıldı', 'text' => 'Faturası kesilerek depo teslimatı gerçekleşti', 'type' => 'success'));
                }else{
                    DB::rollBack();
                    if($durum==-1){
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Fatura Netsise kaydedilemedi', 'type' => 'error'));
                    }else if($durum==2){
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Fatura Açıklaması Kaydedilemedi', 'type' => 'error'));
                    }else if($durum==3){
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Fatura Genel Bilgileri Kaydedilemedi', 'type' => 'error'));
                    }else if($durum==4){
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Fatura Kalemleri Kaydedilemedi', 'type' => 'error'));
                    }else if($durum==5){
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Bugünün Döviz Kuru Netsisten alınamadı', 'type' => 'error'));
                    }else if($durum==6){
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Bu Fatura Numarası Sistemde Kayıtlı', 'type' => 'error'));
                    }else if($durum==7){
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Fatura Kalemleri Kaydedilemedi', 'type' => 'error'));
                    }else{
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => str_replace("'","\'",$durum), 'type' => 'error'));
                    }
                }

            }else{ //bazıları onaylanmış
                try {
                    $kalanlar = BackendController::getListeFark($tumu, $secilenlist);
                    $kalanlist = explode(',', $kalanlar);
                    $kalansayi = count($kalanlist);
                    $depoteslim->secilenler = $kalanlar;
                    $depoteslim->sayacsayisi = $kalansayi;
                    $depoteslim->save();
                } catch (Exception $e) {
                    Log::error($e);
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Teslimatı yapılamadı', 'text' => 'Eski Depo Teslimatı Güncellenemedi', 'type' => 'error'));
                }
                try {
                    $yenidepoteslim = new DepoTeslim;
                    $yenidepoteslim->servis_id = $servisid;
                    $yenidepoteslim->netsiscari_id = $netsiscariid;
                    $yenidepoteslim->secilenler = $secilenler;
                    $yenidepoteslim->sayacsayisi = count($secilenlist);
                    $yenidepoteslim->depodurum = 1;
                    $yenidepoteslim->tipi = $depoteslim->tipi;
                    $yenidepoteslim->periyodik = $depoteslim->periyodik;
                    $yenidepoteslim->subegonderim = $depoteslim->subegonderim;
                    $yenidepoteslim->parabirimi_id=$depoteslim->parabirimi_id;
                    $yenidepoteslim->kullanici_id = Auth::user()->id;
                    $yenidepoteslim->teslimtarihi = date('Y-m-d H:i:s');
                    $yenidepoteslim->carikod = $netsiscari->carikod;
                    $yenidepoteslim->ozelkod = '0';
                    $yenidepoteslim->plasiyerkod = $yetkili->plasiyerkod;
                    $yenidepoteslim->faturaadres = $netsiscari->adres . ' ' . $netsiscari->il . ' ' . $netsiscari->ilce;
                    $yenidepoteslim->teslimadres=$adres;
                    $yenidepoteslim->depokodu = $servis->depokodu;
                    $yenidepoteslim->aciklama = $servis->servisadi;
                    $yenidepoteslim->belge1 = $belge1;
                    $yenidepoteslim->belge2 = $belge2;
                    $yenidepoteslim->netsiskullanici = $yetkili->netsiskullanici;
                    $yenidepoteslim->save();
                } catch (Exception $e) {
                    Log::error($e);
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Teslimatı yapılamadı', 'text' => 'Yeni Depo Teslimatı Kaydedilemedi', 'type' => 'error'));
                    }
                if($fatura==0) //fatura basılmayacaksa
                {
                    $durum=0;
                }else {
                    $durum = BackendController::NetsisDepolararasi($yenidepoteslim->id);
                }
                if($durum==0 || $durum==1)
                {
                    try {
                        foreach ($arizafiyatlar as $arizafiyat) {
                            $degisenler = explode(',', $arizafiyat->degisenler);
                            if (BackendController::getStokKodDurum()) // stok durumu aktifse
                            {
                                $stokdurumlari = StokDurum::whereIn('degisenler_id', $degisenler)->get();
                                foreach ($stokdurumlari as $stokdurum) {
                                    $stokdurum->kalan -= ($stokdurum->adet);
                                    $stokdurum->kullanilan -= ($stokdurum->adet);
                                    $stokdurum->biten += ($stokdurum->adet);
                                    $stokdurum->save();
                                }
                            }
                            $sayacgelen = SayacGelen::find($arizafiyat->sayacgelen_id);
                            $sayacgelen->depoteslim = 1;
                            $sayacgelen->teslimdurum = 4; //depolararası sevk burdan sonra şubede gözükecek //0 bekliyor 1 teslimat 2 geri gönderim 3 hurda 4 depolararasi 5 periyodik bakım
                            $sayacgelen->save();
                            $servistakip = ServisTakip::where('arizafiyat_id', $arizafiyat->id)->first();
                            if($durum){
                                $arizafiyat->durum = 1;
                                $arizafiyat->save();
                                if($servistakip->subetakip_id) {
                                    $subeservistakip = ServisTakip::find($servistakip->subetakip_id);
                                    if ($yenidepoteslim->tipi==3) { //abone teslimine hurda sayaç eklenecek
                                        // abone yoksa onaylama izin verme
                                        $abonesayac = AboneSayac::where('serino', $sayacgelen->serino)->first();
                                        if ($abonesayac) { //abonesayacı mevcut
                                            $abonetahsis = AboneTahsis::where('abonesayac_id', $abonesayac->id)->first();
                                            if ($abonetahsis) {//sayaç aboneye tahsisliyse
                                                try {
                                                    $abone = Abone::find($abonetahsis->abone_id);
                                                    $abonesayacgelen = SayacGelen::where('id', $subeservistakip->sayacgelen_id)->where('serino', $abonesayac->serino)->first();
                                                    try {
                                                        $aboneteslim = AboneTeslim::where('abone_id', $abone->id)->where('teslimdurum', 0)->first();
                                                        if ($aboneteslim) { //teslim edilmemiş o kişiye ait sayaç varsa
                                                            $aboneteslim->secilenler .= ',' . $abonesayacgelen->id;
                                                            $aboneteslim->sayacsayisi += 1;
                                                            $aboneteslim->save();
                                                        } else { // yeni abone teslimi
                                                            $aboneteslim = new AboneTeslim;
                                                            $aboneteslim->abone_id = $abone->id;
                                                            $aboneteslim->uretimyer_id = $abone->uretimyer_id;
                                                            $aboneteslim->netsiscari_id = $abone->netsiscari_id;
                                                            $aboneteslim->secilenler = $abonesayacgelen->id;
                                                            $aboneteslim->sayacsayisi = 1;
                                                            $aboneteslim->teslimdurum = 0;
                                                            $aboneteslim->subekodu = $abone->subekodu;
                                                            $aboneteslim->save();
                                                        }
                                                        BackendController::HurdaKayitEkle($servistakip->id); //depoteslimi hurdakayıdı ya da depocikis olacak
                                                    } catch (Exception $e) {
                                                        Log::error($e);
                                                        DB::rollBack();
                                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Teslimatı Yapılamadı', 'text' => 'Teslim Edilen sayaçlar abone teslimine kaydedilemedi!', 'type' => 'error'));
                                                    }

                                                } catch (Exception $e) {
                                                    Log::error($e);
                                                    DB::rollBack();
                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Teslimatı Yapılamadı', 'text' => 'Teslim Edilen sayaçlardan bir veya birkaçı abone teslimine kaydedilemedi!', 'type' => 'error'));
                                                }
                                            } else {//tahsisli olmayan sayaç mevcut
                                                DB::rollBack();
                                                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Teslimatı Yapılamadı', 'text' => 'Teslim Edilen sayaçlardan bir veya birkaçı aboneye tahsisli değil. Önce tahsis işlemlerinin tamamlanması gerekiyor!', 'type' => 'error'));
                                            }
                                        } else {//listede olmayan sayaç mevcut
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Teslimatı Yapılamadı', 'text' => 'Teslim Edilen sayaçlardan bir veya birkaçı aboneye tahsisli değil. Önce tahsis işlemlerinin tamamlanması gerekiyor!', 'type' => 'error'));
                                        }
                                    } else {
                                        BackendController::DepoTeslimEkle($servistakip->id); //depoteslimi hurdakayıdı ya da depocikis olacak
                                    }
                                }else{
                                    DB::rollBack();
                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Teslimatı Yapılamadı', 'text' => 'Teslim Edilen Sayaçların Şubedeki Kayıdı Bulunamadı!', 'type' => 'error'));
                                }
                            }else{ //hurda sayaç gönderilmeden çıkış yapılır
                                BackendController::DepoCikisEkle($servistakip->id); //depoteslimi hurdakayıdı ya da depocikis olacak
                            }
                            $servistakip->depoteslim_id = $yenidepoteslim->id;
                            $servistakip->durum = 9;
                            $servistakip->depoteslimtarihi = $yenidepoteslim->teslimtarihi;
                            $servistakip->kullanici_id = Auth::user()->id;
                            $servistakip->sonislemtarihi = $yenidepoteslim->teslimtarihi;
                            $servistakip->save();
                            $sayac = Sayac::find($arizafiyat->sayac_id);
                            $sayac->songelistarihi = $sayacgelen->depotarihi;
                            $sayac->save();
                        }
                    }catch (Exception $e){
                        Log::error($e);
                        DB::rollBack();
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Teslimatı Yapılırken Hata Oluştu', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
                    }
                    BackendController::HatirlatmaGuncelle(9,$netsiscariid,$servisid,$sayacsayisi);
                    BackendController::BildirimEkle(9,$netsiscariid,$servisid,$sayacsayisi);
                    if($durum){
                        if($depoteslim->tipi==3)
                            BackendController::HatirlatmaEkle(12,$netsiscariid,6,$sayacsayisi);
                        else
                            BackendController::HatirlatmaEkle(4,$netsiscariid,6,$sayacsayisi);
                    }else{
                        if($depoteslim->tipi==3)
                            BackendController::BildirimEkle(10,$netsiscariid,6,$sayacsayisi);
                        else
                            BackendController::BildirimEkle(9,$netsiscariid,6,$sayacsayisi);
                    }
                    BackendController::IslemEkle(1,Auth::user()->id,'label-success','fa-truck',$yenidepoteslim->id.' Nolu Depo Teslimatı Yapıldı.','Teslim Eden:'.$kullanici->adi_soyadi.',Depo Teslim Numarası:'.$yenidepoteslim->id);
                    DB::commit();
                    if($durum==0)
                        return Redirect::to('depo/depoteslim')->with(array('mesaj' => 'true', 'title' => 'Depo Teslimatı Başarıyla Yapıldı', 'text' => 'Faturası kesilmeden gönderildi', 'type' => 'success'));
                    else
                        return Redirect::to('depo/depoteslim')->with(array('mesaj' => 'true', 'title' => 'Depo Teslimatı Başarıyla Yapıldı', 'text' => 'Faturası kesilerek depo teslimatı gerçekleşti', 'type' => 'success'));
                }else{
                    DB::rollBack();
                    if($durum==-1){
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Fatura Netsise kaydedilemedi', 'type' => 'error'));
                    }else if($durum==2){
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Fatura Açıklaması Kaydedilemedi', 'type' => 'error'));
                    }else if($durum==3){
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Fatura Genel Bilgileri Kaydedilemedi', 'type' => 'error'));
                    }else if($durum==4){
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Fatura Kalemleri Kaydedilemedi', 'type' => 'error'));
                    }else if($durum==5){
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Bugünün Döviz Kuru Netsisten alınamadı', 'type' => 'error'));
                    }else if($durum==6){
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Bu Fatura Numarası Sistemde Kayıtlı', 'type' => 'error'));
                    }else if($durum==7){
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Fatura Kalemleri Kaydedilemedi', 'type' => 'error'));
                    }else{
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => str_replace("'","\'",$durum), 'type' => 'error'));
                    }
                }
            }
        }catch(Exception $e){
            Log::error($e);
            DB::rollBack();
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depolararası Teslimatı yapılamadı', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getHurda($hatirlatma_id=false) {
        if($hatirlatma_id)
            return View::make('depo.hurda',array('hatirlatma_id'=>$hatirlatma_id))->with(array('title'=>'Hurda Sayaçlar Bilgi Ekranı'));
        else
            return View::make('depo.hurda')->with(array('title'=>'Hurda Sayaçlar Bilgi Ekranı'));
    }

    public function postHurdalist() {
        $servis=BackendController::getKullaniciServis();
        $hatirlatma_id=Input::get('hatirlatma_id');
        $netsiscari_id=Input::get('netsiscari_id');
        if($hatirlatma_id!="") {
            $hatirlatma = Hatirlatma::find($hatirlatma_id);
            $query = Hurda::whereIn('hurda.servis_id',$servis)->where('hurda.netsiscari_id',$hatirlatma->netsiscari_id)
                ->select(array("hurda.id","sayac.serino","sayacadi.sayacadi","netsiscari.cariadi","hurdanedeni.nedeni","hurda.hurdatarihi","hurda.ghurdatarihi",
                    "sayacadi.nsayacadi","netsiscari.ncariadi","hurdanedeni.nnedeni"))
                ->leftjoin("netsiscari", "hurda.netsiscari_id", "=", "netsiscari.id")
                ->leftjoin("sayac", "hurda.sayac_id", "=", "sayac.id")
                ->leftjoin("sayacadi", "sayac.sayacadi_id", "=", "sayacadi.id")
                ->leftjoin("hurdanedeni", "hurda.hurdanedeni_id", "=", "hurdanedeni.id");
        }else if($netsiscari_id!=""){
            $netsiscarilist=explode(',',$netsiscari_id);
            $sube=Sube::whereIn('netsiscari_id',$netsiscarilist)->where('aktif',1)->first();
            if($sube) { //TODO düzenlenecek
                $query = Hurda::whereIn('hurda.servis_id', $servis)->whereIn('hurda.netsiscari_id', $netsiscarilist)
                    ->select(array("hurda.id", "sayac.serino", "sayacadi.sayacadi", "netsiscari.cariadi", "hurdanedeni.nedeni", "hurda.hurdatarihi", "hurda.ghurdatarihi",
                        "sayacadi.nsayacadi", "netsiscari.ncariadi", "hurdanedeni.nnedeni"))
                    ->leftjoin("netsiscari", "hurda.netsiscari_id", "=", "netsiscari.id")
                    ->leftjoin("sayac", "hurda.sayac_id", "=", "sayac.id")
                    ->leftjoin("sayacadi", "sayac.sayacadi_id", "=", "sayacadi.id")
                    ->leftjoin("hurdanedeni", "hurda.hurdanedeni_id", "=", "hurdanedeni.id");
            }else{
                $query = Hurda::whereIn('hurda.servis_id', $servis)->whereIn('hurda.netsiscari_id', $netsiscarilist)
                    ->select(array("hurda.id", "sayac.serino", "sayacadi.sayacadi", "netsiscari.cariadi", "hurdanedeni.nedeni", "hurda.hurdatarihi", "hurda.ghurdatarihi",
                        "sayacadi.nsayacadi", "netsiscari.ncariadi", "hurdanedeni.nnedeni"))
                    ->leftjoin("netsiscari", "hurda.netsiscari_id", "=", "netsiscari.id")
                    ->leftjoin("sayac", "hurda.sayac_id", "=", "sayac.id")
                    ->leftjoin("sayacadi", "sayac.sayacadi_id", "=", "sayacadi.id")
                    ->leftjoin("hurdanedeni", "hurda.hurdanedeni_id", "=", "hurdanedeni.id");
            }

        }else{
            $query = Hurda::whereIn('hurda.servis_id',$servis)
                ->select(array("hurda.id","sayac.serino","sayacadi.sayacadi","netsiscari.cariadi","hurdanedeni.nedeni","hurda.hurdatarihi","hurda.ghurdatarihi",
                    "sayacadi.nsayacadi","netsiscari.ncariadi","hurdanedeni.nnedeni"))
                ->leftjoin("netsiscari", "hurda.netsiscari_id", "=", "netsiscari.id")
                ->leftjoin("sayac", "hurda.sayac_id", "=", "sayac.id")
                ->leftjoin("sayacadi", "sayac.sayacadi_id", "=", "sayacadi.id")
                ->leftjoin("hurdanedeni", "hurda.hurdanedeni_id", "=", "hurdanedeni.id");
        }
        return Datatables::of($query)
            ->editColumn('hurdatarihi', function ($model) {
                $date = new DateTime($model->hurdatarihi);
                return $date->format('d-m-Y');})
            ->addColumn('islemler', function ($model) {
                return "<a class='btn btn-sm btn-warning detay' href='#detay-goster' data-toggle='modal' data-id='{$model->id}'> Detay </a>";
            })
            ->make(true);
    }

    public function getHurdabilgi() {
        try {
            $id=Input::get('id');
            $hurda = Hurda::find($id);
            $hurda->hurdatarihi = date("d-m-Y", strtotime($hurda->hurdatarihi));
            $hurda->hurdanedeni = HurdaNedeni::find($hurda->hurdanedeni_id);
            $hurda->sayac = Sayac::find($hurda->sayac_id);
            $hurda->sayacadi = SayacAdi::find($hurda->sayac->sayacadi_id);
            $sayacgelen = SayacGelen::find($hurda->sayacgelen_id);
            $sayacgelen->depotarihi = date("d-m-Y", strtotime($sayacgelen->depotarihi));
            return Response::json(array('durum'=>true,'hurda' => $hurda, 'sayacgelen' => $sayacgelen));
        } catch (Exception $e) {
            return Response::json(array('durum' => false, 'title' => 'Hurda Sayaç Bilgisinde Hata', 'text' => str_replace("'","\'",$e->getMessage())));
        }
    }

    public function getDepogelenduzenle($id) {
        $depogelen=DepoGelen::find($id);
        $depogelenler=DepoGelen::where('fisno',$depogelen->fisno)->get(array('id'));
        $depogelenlist=$depogelenler->toArray();
        $sayacgelenler=SayacGelen::whereIn('depogelen_id',$depogelenlist)->get();
        $sayacadlari=$sayaccaplari=$servisstokkodlari=$uretimyerleri=$netsiscari=array();
        $flag=0;
        $durum = "1";
        if ($depogelen->db_name != 'MANAS' . date('Y')) { //eski depo girişi guncelleniyorsa
            $durum = "0";
        }
        switch ($depogelen->servis_id){
            case 1:case 4:
                foreach($sayacgelenler as $sayacgelen)
                {
                    $sayacgelen->uretimyer=UretimYer::find($sayacgelen->uretimyer_id);
                    $sayacgelen->stokkod=ServisStokKod::where('stokkodu',$sayacgelen->stokkodu)->first();
                    $sayacgelen->sayacadi=SayacAdi::find($sayacgelen->sayacadi_id);
                    $sayacgelen->sayaccap=SayacCap::find($sayacgelen->sayaccap_id);
                    if($sayacgelen->arizakayit){
                        $flag=1;
                    }
                }
                $depogelen->netsiscari=NetsisCari::where('carikod',$depogelen->carikod)->first();
                $sayacadlari = SayacAdi::whereIn('sayactur_id',BackendController::getServisSayacTur($depogelen->servis_id))->get();
                $sayaccaplari = SayacCap::all();
                $servisstokkodlari = ServisStokKod::whereIn('servisid',BackendController::getServisSayacTur($depogelen->servis_id))->where('koddurum',1)->get();
                if($flag==0){
                    $netsiscari = NetsisCari::where('caridurum','A')->whereIn('caritipi',array('A','D'))
                        ->whereIn('subekodu',array(-1,8))->whereNotIn('carikod',(function ($query){$query->select('carikod')->from('kodharichar')->where('subekodu', 8);}))
                        ->orderBy('carikod','asc')->get();
                }else{
                    $netsiscari = NetsisCari::where('carikod',$depogelen->carikod)->get();
                }
                $uretimyerleri = UretimYer::where('mekanik',0)->where('id','<>',0)->whereNotNull('oracle_id')->get();
                return View::make('depo.depogelenduzenle',array('sube'=>null,'depogelen'=>$depogelen,'depogelenler'=>$depogelenler,'sayacgelen'=>$sayacgelenler,'sayacadlari'=>$sayacadlari,'sayaccaplari'=>$sayaccaplari,'uretimyerleri'=>$uretimyerleri,'netsiscariler'=>$netsiscari,'servisstokkodlari'=>$servisstokkodlari,'durum'=>$durum))->with(array('title'=>'Depo Gelen Bilgisi Düzenle'));
            case 2:
                foreach($sayacgelenler as $sayacgelen)
                {
                    $sayacgelen->uretimyer=UretimYer::find($sayacgelen->uretimyer_id);
                    $sayacgelen->stokkod=ServisStokKod::where('stokkodu',$sayacgelen->stokkodu)->first();
                    $sayacgelen->sayacadi=SayacAdi::find($sayacgelen->sayacadi_id);
                    if($sayacgelen->arizakayit){
                        $flag=1;
                    }
                }
                $depogelen->netsiscari=NetsisCari::where('carikod',$depogelen->carikod)->first();
                $sayacadlari = SayacAdi::whereIn('sayactur_id',BackendController::getServisSayacTur($depogelen->servis_id))->get();
                $servisstokkodlari = ServisStokKod::whereIn('servisid',BackendController::getServisSayacTur($depogelen->servis_id))->where('koddurum',1)->get();
                if($flag==0){
                    $netsiscari = NetsisCari::where('caridurum','A')->whereIn('caritipi',array('A','D'))
                        ->whereIn('subekodu',array(-1,8))->whereNotIn('carikod',(function ($query){$query->select('carikod')->from('kodharichar')->where('subekodu', 8);}))
                        ->orderBy('carikod','asc')->get();
                }else{
                    $netsiscari = NetsisCari::where('carikod',$depogelen->carikod)->get();
                }
                $uretimyerleri = UretimYer::where('mekanik',0)->where('id','<>',0)->get();
                return View::make('depo.depogelenduzenle',array('sube'=>null,'depogelen'=>$depogelen,'depogelenler'=>$depogelenler,'sayacgelen'=>$sayacgelenler,'sayacadlari'=>$sayacadlari,'uretimyerleri'=>$uretimyerleri,'netsiscariler'=>$netsiscari,'servisstokkodlari'=>$servisstokkodlari,'durum'=>$durum))->with(array('title'=>'Depo Gelen Bilgisi Düzenle'));
                break;
            case 3:
                foreach($sayacgelenler as $sayacgelen)
                {
                    $sayacgelen->uretimyer=UretimYer::find($sayacgelen->uretimyer_id);
                    $sayacgelen->stokkod=ServisStokKod::where('stokkodu',$sayacgelen->stokkodu)->first();
                    $sayacgelen->sayacadi=SayacAdi::find($sayacgelen->sayacadi_id);
                    if($sayacgelen->arizakayit){
                        $flag=1;
                    }
                }
                $depogelen->netsiscari=NetsisCari::where('carikod',$depogelen->carikod)->first();
                $sayacadlari = SayacAdi::whereIn('sayactur_id',BackendController::getServisSayacTur($depogelen->servis_id))->get();
                $servisstokkodlari = ServisStokKod::whereIn('servisid',BackendController::getServisSayacTur($depogelen->servis_id))->where('koddurum',1)->get();
                if($flag==0){
                    $netsiscari = NetsisCari::where('caridurum','A')->whereIn('caritipi',array('A','D'))
                        ->whereIn('subekodu',array(-1,8))->whereNotIn('carikod',(function ($query){$query->select('carikod')->from('kodharichar')->where('subekodu', 8);}))
                        ->orderBy('carikod','asc')->get();
                }else{
                    $netsiscari = NetsisCari::where('carikod',$depogelen->carikod)->get();
                }
                $uretimyerleri = UretimYer::where('mekanik',0)->where('id','<>',0)->whereNotNull('oracle_id')->get();
                return View::make('depo.depogelenduzenle',array('sube'=>null,'depogelen'=>$depogelen,'depogelenler'=>$depogelenler,'sayacgelen'=>$sayacgelenler,'sayacadlari'=>$sayacadlari,'uretimyerleri'=>$uretimyerleri,'netsiscariler'=>$netsiscari,'servisstokkodlari'=>$servisstokkodlari,'durum'=>$durum))->with(array('title'=>'Depo Gelen Bilgisi Düzenle'));
                break;
            case 5:
                foreach($sayacgelenler as $sayacgelen)
                {
                    $sayacgelen->uretimyer=UretimYer::find($sayacgelen->uretimyer_id);
                    $sayacgelen->stokkod=ServisStokKod::where('stokkodu',$sayacgelen->stokkodu)->first();
                    $sayacgelen->sayacadi=SayacAdi::find($sayacgelen->sayacadi_id);
                    $sayacgelen->sayac=Sayac::where('sayactur_id',5)->where('serino',$sayacgelen->serino)->where('uretimyer_id',$sayacgelen->uretimyer_id)->first();
                    if($sayacgelen->arizakayit){
                        $flag=1;
                    }
                }
                $depogelen->netsiscari=NetsisCari::where('carikod',$depogelen->carikod)->first();
                $sayacadlari = SayacAdi::whereIn('sayactur_id',BackendController::getServisSayacTur($depogelen->servis_id))->get();
                $servisstokkodlari = ServisStokKod::whereIn('servisid',BackendController::getServisSayacTur($depogelen->servis_id))->where('koddurum',1)->get();
                if($flag==0){
                    $netsiscari = NetsisCari::where('caridurum','A')->whereIn('caritipi',array('A','D'))
                        ->whereIn('subekodu',array(-1,8))->whereNotIn('carikod',(function ($query){$query->select('carikod')->from('kodharichar')->where('subekodu', 8);}))
                        ->orderBy('carikod','asc')->get();
                }else{
                    $netsiscari = NetsisCari::where('carikod',$depogelen->carikod)->get();
                }
                $yillar = array_combine(range(date("Y"), 1990), range(date("Y"), 1990));
                $uretimyerleri = UretimYer::where('mekanik',1)->where('id','<>',0)->get();
                return View::make('depo.depogelenduzenle',array('sube'=>null,'depogelen'=>$depogelen,'depogelenler'=>$depogelenler,'sayacgelen'=>$sayacgelenler,'sayacadlari'=>$sayacadlari,'yillar'=>$yillar,'uretimyerleri'=>$uretimyerleri,'netsiscariler'=>$netsiscari,'servisstokkodlari'=>$servisstokkodlari,'durum'=>$durum))->with(array('title'=>'Depo Gelen Bilgisi Düzenle'));
                break;
            case 6:
                $netsiscariid=Auth::user()->netsiscari_id;
                $uretimyerid=CariYer::whereIn('netsiscari_id',$netsiscariid)->where('durum',1)->get(array('uretimyer_id'))->toArray();
                $uretimyerleri = UretimYer::whereIn('id',$uretimyerid)->get();
                $sube=Sube::whereIn('netsiscari_id',$netsiscariid)->where('aktif',1)->first();
                if($sube){
                    $sayacturlist=explode(',',$sube->sayactur);
                    $sayacadilist=explode(',',$sube->sayacadlari);
                    $sayacadlari = SayacAdi::whereIn('sayactur_id',$sayacturlist)->whereIn('id',$sayacadilist)->get();
                    $sayaccaplari = SayacCap::all();
                    $sayacadilist=SayacAdi::whereIn('sayactur_id',$sayacturlist)->whereIn('id',$sayacadilist)->get(array('id'))->toArray();
                    $sayacparcalari = SayacParca::whereIn('sayacadi_id',$sayacadilist)->get(array('servisstokkod_id'))->toArray();
                    $servisstokkodlari = ServisStokKod::where('servisid','<>',0)->whereIn('servisid',$sayacturlist)->whereIn('id',$sayacparcalari)->where('koddurum',true)->get();
                }else{
                    $sayacadlari = SayacAdi::all();
                    $sayaccaplari = SayacCap::all();
                    $servisstokkodlari = ServisStokKod::where('servisid','<>',0)->where('koddurum',true)->get();
                }
                foreach($sayacgelenler as $sayacgelen)
                {
                    $sayacgelen->uretimyer=UretimYer::find($sayacgelen->uretimyer_id);
                    $sayacgelen->stokkod=ServisStokKod::where('stokkodu',$sayacgelen->stokkodu)->first();
                    $sayacgelen->sayacadi=SayacAdi::find($sayacgelen->sayacadi_id);
                    $sayacgelen->sayaccap=SayacCap::find($sayacgelen->sayaccap_id);
                    if($sayacgelen->arizakayit || $sayacgelen->depolararasi){
                        $flag=1;
                    }
                }
                $depogelen->netsiscari=NetsisCari::where('carikod',$depogelen->carikod)->first();
                if($flag==0){
                    $netsiscari = NetsisCari::where('caridurum','A')->whereIn('caritipi',array('A','D'))
                        ->where(function($query)use($netsiscariid,$sube){$query->whereIn('id',$netsiscariid)->orwhereIn('subekodu',array(-1,$sube->subekodu));})
                        ->whereNotIn('carikod',(function ($query) use ($sube) {$query->select('carikod')->from('kodharichar')->where('subekodu', $sube->subekodu);}))
                        ->orderBy('cariadi','asc')->get();
                }else{
                    $netsiscari = NetsisCari::where('carikod',$depogelen->carikod)->get();
                }
                return View::make('depo.depogelenduzenle',array('sube'=>$sube,'depogelen'=>$depogelen,'depogelenler'=>$depogelenler,'sayacgelen'=>$sayacgelenler,'sayacadlari'=>$sayacadlari,'sayaccaplari'=>$sayaccaplari,'uretimyerleri'=>$uretimyerleri,'netsiscariler'=>$netsiscari,'servisstokkodlari'=>$servisstokkodlari,'durum'=>$durum))->with(array('title'=>'Depo Gelen Bilgisi Düzenle'));
                break;
        }
        return View::make('depo.depogelenduzenle',array('depogelen'=>$depogelen,'depogelenler'=>$depogelenler,'sayacgelen'=>$sayacgelenler,'sayacadlari'=>$sayacadlari,'sayaccaplari'=>$sayaccaplari,'uretimyerleri'=>$uretimyerleri,'netsiscariler'=>$netsiscari,'servisstokkodlari'=>$servisstokkodlari,'durum'=>$durum))->with(array('title'=>'Depo Gelen Bilgisi Düzenle'));
    }

    public function postDepogelenduzenle($id) {
        try {
            $rules = ['gelis' => 'required', 'uretimyerleri' => 'required', 'cariadi' => 'required', 'serino' => 'required', 'sayacadlari' => 'required', 'sayaccaplari' => 'required', 'serviskodlari' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            $depogelen = DepoGelen::find($id);
            $dbname = $depogelen->db_name;
            $eskidbname = $depogelen->db_name;
            $tarih = Input::get('gelis');
            $gelistarih = date("Y-m-d", strtotime($tarih));
            $uretimyerleri = Input::get('uretimyerleri');
            $netsiscari_id = Input::get('cariadi');
            $serinolar = Input::get('serino');
            $serviskodlari = Input::get('serviskodlari');
            $sayacadlari = Input::get('sayacadlari');
            $sayaccaplari = Input::get('sayaccaplari');
            $subekodu = Input::get('subekodu');
            if(Input::has('uretimtarih'))
                $uretimyillari=Input::get('uretimtarih');
            else {
                $uretimyillari = array();
                for ($i = 0; $i < count($serinolar); $i++) {
                    array_push($uretimyillari, '');
                }
            }
            $netsiscari = NetsisCari::find($netsiscari_id);
            $belgeno = Input::get('belgeno');
            $servisid = $depogelen->servis_id;
            if($servisid==6){
                $nedenler = Input::get('neden');
                $endeksler = Input::get('endeks');
                $takilmatarihleri = Input::get('takilmatarihi');
                $subeyetkili = SubeYetkili::where('kullanici_id', Auth::user()->id)->where('aktif',1)->first();
                if (!$subeyetkili){
                    Input::flash();
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kullanıcı Yetkili Değil', 'text' => 'Kullanıcı Şube için Yetkili Olarak Eklenmemiş!', 'type' => 'error'));
                }
                if (count($serinolar) > 0) {
                    for ($i = 0; $i < count($serinolar); $i++) {
                        $serino1 = $serinolar[$i];
                        $sayacadi=$sayacadlari[$i];
                        $yeri = $uretimyerleri[$i];
                        if ($serino1 == "")
                            continue;
                        $servistakip = ServisTakip::where('serino', $serino1)->where('depogelen_id', $depogelen->id)->first();
                        if ($servistakip) {
                            if($servistakip->arizakayit_id==null)
                                if(BackendController::SayacDurum($serino1,$yeri,$servisid,1, $servistakip->id,$sayacadi)) {
                                    Input::flash();
                                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Seri No Hatası', 'text' => $serino1 . ' Nolu Sayacın Depo Çıkışı Yapılmamış.Önce Sayaca Ait Diğer İşlemleri Bitiriniz.', 'type' => 'error'));
                                }
                        }
                        if(isset($uretimyerleri[$i])){
                            if($uretimyerleri[$i]==""){
                                Input::flash();
                                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Geliş Yeri Hatası', 'text' => 'Seri Numarası Girilen Sayacın Geliş Yeri Boş Geçilmiş!', 'type' => 'error'));
                            }
                        }
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
                        if(isset($serviskodlari[$i])){
                            if($serviskodlari[$i]==""){
                                Input::flash();
                                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Servis Kodu Hatası', 'text' => 'Seri Numarası Girilen Sayacın İstek Kısmı Boş Geçilmiş!', 'type' => 'error'));
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
                    $kullanici = Kullanici::find(Auth::user()->id);
                    if ($netsiscari->caridurum != "A") {
                        Input::flash();
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Netsis Cari Uyarısı', 'text' => 'Cari Bilgisi Netsis Üzerinde Kilitli.', 'type' => 'warning'));
                    }
                    $sayaclar = BackendController::SubeDepoGirisGrupla($serinolar, $uretimyerleri, $serviskodlari, $sayacadlari, $sayaccaplari, $nedenler,$takilmatarihleri, $endeksler);
                    if (count($sayaclar) == 0) {
                        Input::flash();
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Ambar Girişi Kaydedilemedi', 'text' => 'Girilen Sayaçların Seri Numaraları yazılmamış', 'type' => 'error'));
                    }
                    DB::beginTransaction();
                    $depogiris = BackendController::SubeDepoGirisDuzenle($dbname,$sayaclar,$gelistarih,$netsiscari_id,$belgeno);
                    //Config::set('database.connections.sqlsrv2.database', 'MANAS' . date('Y'));
                    if ($depogiris['durum'] == '0') {
                        DB::rollBack();
                        Input::flash();
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Ambar Girişi Kaydedilemedi', 'text' => $depogiris['text'], 'type' => 'error'));
                    }
                    $inckeys = $depogiris['faturakalemler'];
                    $dbnames = $depogiris['dbnames'];
                    if ($dbname != 'MANAS' . date('Y')) { //eski depo girişi guncelleniyorsa
                        $dbname = 'MANAS'.date('Y');
                    }
                    try {
                        for ($i=0;$i<count($inckeys);$i++) {
                            if ($dbname != $dbnames[$i]) { //eski depo girişi
                                $depogelen = DepoGelen::where('db_name', $dbnames[$i])->where('inckeyno', $inckeys[$i])->first();
                                if ($depogelen) {
                                    // eksilen depo kayıdı
                                    // düzenlenen depo kayıdı
                                    // yeni yok
                                    $depogelen->kod = ServisStokKod::where('stokkodu', $depogelen->servisstokkodu)->first();
                                    foreach ($sayaclar as $sayacgrup) {
                                        if ($sayacgrup['kod'] == $depogelen->kod->id) {
                                            $sayac = $sayacgrup['sayac']; //eklenecek sayaçlar
                                            $eskiler = SayacGelen::where('depogelen_id', $depogelen->id)->get();
                                            $eskisayaclar = SayacGelen::where('depogelen_id', $depogelen->id)->get();
                                            foreach ($eskisayaclar as $eskisayac) {
                                                $eskisayac->flag = 0;
                                            }
                                            foreach ($sayac as $girilecek) {
                                                $serino = $girilecek['serino'];
                                                $sayacadi = $girilecek['sayacadi'];
                                                $sayaccap = $girilecek['sayaccap'];
                                                $uretimyeri = $girilecek['uretimyeri'];
                                                $sokulmenedeni = $girilecek['neden'];
                                                $takilmatarih = $girilecek['takilmatarihi'];
                                                $endeks = $girilecek['endeks'];
                                                if ($serino != "") {
                                                    try {
                                                        for ($j = 0; $j < $eskiler->count(); $j++) {
                                                            $sayacgelen = $eskiler[$j];
                                                            $eskisayac = $eskisayaclar[$j];
                                                            if ($sayacgelen->serino == $serino && $eskisayac->flag == 0) {
                                                                try {
                                                                    $eskisayac->flag = 1;
                                                                    if(!$sayacgelen->arizakayit && !$sayacgelen->depolararasi){
                                                                        $sayacgelen->depogelen_id = $depogelen->id;
                                                                        $sayacgelen->netsiscari_id = $netsiscari_id;
                                                                        $sayacgelen->stokkodu = $depogelen->servisstokkodu;
                                                                        $sayacgelen->serino = $serino;
                                                                        $sayacgelen->depotarihi = $depogelen->tarih;
                                                                        $sayacgelen->sayacadi_id = $sayacadi;
                                                                        $sayacgelen->sayaccap_id = $sayaccap;
                                                                        $sayacgelen->uretimyer_id = $uretimyeri;
                                                                        $sayacgelen->kullanici_id = Auth::user()->id;
                                                                        $sayacgelen->sokulmenedeni = $sokulmenedeni;
                                                                        $sayacgelen->takilmatarihi = $takilmatarih=="" ? NULL : date("Y-m-d", strtotime($takilmatarih));
                                                                        $sayacgelen->endeks = $endeks;
                                                                        $sayacgelen->save();
                                                                        if ($sayacgelen->servis_id == 5) {
                                                                            $sayac = Sayac::where('serino', $serino)->where('sayactur_id', 5)->where('uretimyer_id', $uretimyeri)->first();
                                                                            if ($sayac) {
                                                                                $sayac->sayacadi_id = $sayacadi;
                                                                                $sayac->sayaccap_id = $sayaccap;
                                                                                $sayac->uretimyer_id = $uretimyeri;
                                                                                $sayac->save();
                                                                            } else {
                                                                                $sayac = new Sayac;
                                                                                $sayac->serino = $serino;
                                                                                $sayac->cihazno = $serino;
                                                                                $sayac->sayactur_id = 5;
                                                                                $sayac->sayacadi_id = $sayacadi;
                                                                                $sayac->sayaccap_id = 1;
                                                                                $sayac->uretimyer_id = $uretimyeri;
                                                                                $sayac->save();
                                                                            }
                                                                        }
                                                                        $servistakip = ServisTakip::where('sayacgelen_id', $sayacgelen->id)->first();
                                                                        if ($servistakip) {
                                                                            $servistakip->depogelen_id = $depogelen->id;
                                                                            $servistakip->serino = $serino;
                                                                            $servistakip->sayacadi_id = $sayacadi;
                                                                            $servistakip->netsiscari_id = $netsiscari_id;
                                                                            $servistakip->uretimyer_id = $uretimyeri;
                                                                            $servistakip->save();
                                                                        }
                                                                        $abonesayac = AboneSayac::where('serino',$serino)->first();
                                                                        if($abonesayac){
                                                                            $abonetahsis = AboneTahsis::where('abonesayac_id',$abonesayac->id)->first();
                                                                            if($abonetahsis){
                                                                                $serviskayit = ServisKayit::where('depogelen_id',$depogelen->id)->where('abonetahsis_id',$abonetahsis->id)->first();
                                                                                if($serviskayit){
                                                                                    $serviskayit->netsiscari_id = $netsiscari_id;
                                                                                    $serviskayit->uretimyer_id = $uretimyeri;
                                                                                    $serviskayit->sokulmedurumu = 1;
                                                                                    $serviskayit->aciklama = $sayacgelen->sokulmenedeni;
                                                                                    $serviskayit->servissayaci = $sayacgelen->takilmatarihi==NULL ? 0 : 1;
                                                                                    $serviskayit->takilmatarihi = $sayacgelen->takilmatarihi;
                                                                                    $serviskayit->ilkendeks = $sayacgelen->endeks;
                                                                                    $serviskayit->save();
                                                                                }
                                                                            }
                                                                        }

                                                                        $depolararasi = Depolararasi::where('netsiscari_id', $eskisayac->netsiscari_id)->where('secilenler', 'LIKE', '%' . $sayacgelen->id . '%')
                                                                            ->where('servis_id', 6)->where('tipi', 0)->where('depodurum', 0)->get();
                                                                        foreach ($depolararasi as $depoislem) {
                                                                            $secilenlist = explode(',', $depoislem->secilenler);
                                                                            if (in_array($sayacgelen->id, $secilenlist)) { //sayaç bu listedeyse
                                                                                if(count($secilenlist)>1){ // sayaç bu listeden ayrılacak
                                                                                    $secilenler = "";
                                                                                    foreach ($secilenlist as $secilen) {
                                                                                        if ($secilen != $sayacgelen->id)
                                                                                            $secilenler = ($secilenler == "" ? "" : ",") . $secilen;
                                                                                    }
                                                                                    $depoislem->secilenler = $secilenler;
                                                                                    $depoislem->save();


                                                                                    $yenidepoislem = Depolararasi::where('servis_id', $servisid)->where('netsiscari_id', $netsiscari_id)->where('depokodu', $depogelen->depokodu)
                                                                                        ->where('tipi', 0)->where('depodurum', 0)->first();
                                                                                    if ($yenidepoislem) {
                                                                                        $yenidepoislem->secilenler .= ($yenidepoislem->secilenler == "" ? "" : ",") . $sayacgelen->id; //bu bilgiler yok
                                                                                        $yenidepoislem->sayacsayisi += 1;
                                                                                    } else {
                                                                                        $yenidepoislem = new Depolararasi;
                                                                                        $yenidepoislem->subekodu = $subekodu;
                                                                                        $yenidepoislem->servis_id = $servisid;
                                                                                        $yenidepoislem->netsiscari_id = $sayacgelen->netsiscari_id;
                                                                                        $yenidepoislem->secilenler = $sayacgelen->id;
                                                                                        $yenidepoislem->sayacsayisi = 1;
                                                                                        $yenidepoislem->depodurum = 0;
                                                                                        $yenidepoislem->depokodu = $depogelen->depokodu;
                                                                                        $yenidepoislem->save();
                                                                                    }
                                                                                }else{
                                                                                    $depoislem->netsiscari_id = $sayacgelen->netsiscari_id;
                                                                                    $depoislem->secilenler = $sayacgelen->id;
                                                                                    $depoislem->sayacsayisi = 1;
                                                                                    $depoislem->depodurum = 0;
                                                                                    $depoislem->depokodu = $depogelen->depokodu;
                                                                                    $depoislem->save();
                                                                                }
                                                                                break;
                                                                            }
                                                                        }
                                                                        array_push($allids, $sayacgelen->id);
                                                                    }
                                                                    break;
                                                                } catch (Exception $e) {
                                                                    Log::error($e);
                                                                    DB::rollBack();
                                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Kayıdı Güncellenemedi', 'text' => 'Sayac Kayıdı Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                                                }
                                                            } else {
                                                                continue;
                                                            }
                                                        }
                                                    } catch (Exception $e) {
                                                        Log::error($e);
                                                        DB::rollBack();
                                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayac Kayıdı Yapılamadı', 'text' => 'Sayac Kayıdı Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                                    }
                                                }
                                            }
                                            for ($j = 0; $j < $eskisayaclar->count(); $j++) {
                                                $sayacgelen = $eskisayaclar[$j];
                                                if ($sayacgelen->flag == 0) { // silinecek varsa
                                                    try {
                                                        $sayacgelen = SayacGelen::find($sayacgelen->id);
                                                        $servistakip = ServisTakip::where('sayacgelen_id', $sayacgelen->id)->first();
                                                        if ($sayacgelen->servis_id == 5) {
                                                            $sayac = Sayac::where('serino', $sayacgelen->serino)->where('uretimyer_id', $sayacgelen->uretimyeri_id)
                                                                ->where('sayactur_id', 5)->first();
                                                            if ($sayac) { // mekanik sayaç varsa ve başka kayıdı yoksa silinecek
                                                                $eskisayacgelen = SayacGelen::where('serino', $sayacgelen->serino)->where('sayactur_id', 5)->where('id', '<>', $sayacgelen->id)->first();
                                                                if (!$eskisayacgelen)
                                                                    $sayac->delete();
                                                            }
                                                        }
                                                        $depolararasi = Depolararasi::where('netsiscari_id', $netsiscari_id)->where('secilenler', 'LIKE', '%' . $sayacgelen->id . '%')
                                                            ->where('servis_id', 6)->where('tipi', 0)->where('depodurum', 0)->get();
                                                        foreach ($depolararasi as $depoislem) {
                                                            $secilenlist = explode(',', $depoislem->secilenler);
                                                            if (in_array($sayacgelen->id, $secilenlist)) { //sayaç bu listedeyse
                                                                $secilenler = "";
                                                                foreach ($secilenlist as $secilen) {
                                                                    if ($secilen != $sayacgelen->id)
                                                                        $secilenler = ($secilenler == "" ? "" : ",") . $secilen;
                                                                }
                                                                if ($secilenler != "") {
                                                                    $depoislem->secilenler = $secilenler;
                                                                    $depoislem->save();
                                                                } else {
                                                                    $depoislem->delete();
                                                                }
                                                                break;
                                                            }
                                                        }
                                                        $abonesayac = AboneSayac::where('serino', $sayacgelen->serino)->first();
                                                        if ($abonesayac) {
                                                            $abonetahsis = AboneTahsis::where('abonesayac_id', $abonesayac->id)->first();
                                                            if ($abonetahsis) {
                                                                $serviskayit = ServisKayit::where('depogelen_id', $depogelen->id)->where('abonetahsis_id', $abonetahsis->id)->first();
                                                                if ($serviskayit) {
                                                                    $serviskayit->depogelen_id = null;
                                                                    $serviskayit->sokulmedurumu = 0;
                                                                    $serviskayit->aciklama = '';
                                                                    $serviskayit->servissayaci = $sayacgelen->takilmatarihi==NULL ? 0 : 1;
                                                                    $serviskayit->takilmatarihi = $sayacgelen->takilmatarihi;
                                                                    $serviskayit->ilkendeks = $sayacgelen->endeks;
                                                                    $serviskayit->save();
                                                                }
                                                            }
                                                        }
                                                        $servistakip->delete();
                                                        $sayacgelen->delete();
                                                        BackendController::HatirlatmaSil(3, $netsiscari_id, $servisid, 1, $depogelen->id, $depogelen->servisstokkodu);
                                                        BackendController::HatirlatmaSil(11, $netsiscari_id, $servisid, 1, $depogelen->id, $depogelen->servisstokkodu);
                                                        BackendController::BildirimGeriAl(2, $netsiscari_id, $servisid, 1, $depogelen->id, $depogelen->servisstokkodu);
                                                    } catch (Exception $e) {
                                                        Log::error($e);
                                                        DB::rollBack();
                                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Eski Sayaç Kayıdı Silinemedi', 'text' => 'Eski Sayaç Kayıdı Silinirken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }else{
                                    DB::rollBack();
                                    Input::flash();
                                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Depo Giriş Bilgisi Güncellenemedi', 'text' => 'Depo Gelen Bilgisi Kaydedilmemiş', 'type' => 'error'));
                                }
                            } else {
                                $depogelen = DepoGelen::where('db_name', $dbname)->where('inckeyno', $inckeys[$i])->first();
                                $now = time();
                                while ($now + 30 > time()) {
                                    $depogelen = DepoGelen::where('db_name', $dbname)->where('inckeyno', $inckeys[$i])->first();
                                    if ($depogelen)
                                        break;
                                }
                                if ($depogelen) {
                                    $hatirlatmalar = Hatirlatma::where('depogelen_id', $depogelen->id)->get();
                                    foreach ($hatirlatmalar as $hatirlatma) {
                                        $hatirlatma->netsiscari_id = $netsiscari->id;
                                        $hatirlatma->save();
                                    }
                                    $depogelen->kod = ServisStokKod::where('stokkodu', $depogelen->servisstokkodu)->first();
                                    foreach ($sayaclar as $sayacgrup) {
                                        if ($sayacgrup['kod'] == $depogelen->kod->id) {
                                            $sayac = $sayacgrup['sayac']; //eklenecek sayaçlar
                                            if($eskidbname!=$dbname){ //eski tarihli sayaçlar için işlem olmayacak
                                                $biten = 0;
                                                $eskidepogelen = SayacGelen::where('netsiscari_id',$netsiscari_id)->where('depotarihi',$gelistarih)
                                                    ->where('stokkodu',$depogelen->servisstokkodu)->where('serino',$sayac[0]['serino'])->first(array('depogelen_id'));
                                                $eskiler = SayacGelen::where('depogelen_id', $eskidepogelen->depogelen_id)->get();
                                                $eskisayaclar = SayacGelen::where('depogelen_id', $eskidepogelen->depogelen_id)->get();
                                                foreach ($sayac as $girilecek) {
                                                    $serino = $girilecek['serino'];
                                                    $sayacadi = $girilecek['sayacadi'];
                                                    $sayaccap = $girilecek['sayaccap'];
                                                    $uretimyeri = $girilecek['uretimyeri'];
                                                    $sokulmenedeni = $girilecek['neden'];
                                                    $takilmatarih = $girilecek['takilmatarihi'];
                                                    $endeks = $girilecek['endeks'];
                                                    if ($serino != "") {
                                                        try {
                                                            $flag = 0;
                                                            for ($j = 0; $j < $eskiler->count(); $j++) {
                                                                $sayacgelen = $eskiler[$j];
                                                                $eskisayac = $eskisayaclar[$j];
                                                                if ($sayacgelen->serino == $serino) {
                                                                    $flag = 1;
                                                                    $eskisayac->flag = 1;
                                                                    break;
                                                                } else {
                                                                    continue;
                                                                }
                                                            }
                                                            if ($flag == 0) { //yeni eklemeyse
                                                                try {
                                                                    $sayacgelen = new SayacGelen;
                                                                    $sayacgelen->depogelen_id = $depogelen->id;
                                                                    $sayacgelen->netsiscari_id = $netsiscari_id;
                                                                    $sayacgelen->stokkodu = $depogelen->servisstokkodu;
                                                                    $sayacgelen->serino = $serino;
                                                                    $sayacgelen->depotarihi = $depogelen->tarih;;
                                                                    $sayacgelen->sayacadi_id = $sayacadi;
                                                                    $sayacgelen->sayaccap_id = $sayaccap;
                                                                    $sayacgelen->uretimyer_id = $uretimyeri;
                                                                    $sayacgelen->servis_id = $servisid;
                                                                    $sayacgelen->subekodu = $subekodu;
                                                                    $sayacgelen->kullanici_id = $kullanici->id;
                                                                    $sayacgelen->beyanname = -2;
                                                                    $sayacgelen->sokulmenedeni = $sokulmenedeni;
                                                                    $sayacgelen->takilmatarihi = $takilmatarih=="" ? NULL : date("Y-m-d", strtotime($takilmatarih));
                                                                    $sayacgelen->endeks = $endeks;
                                                                    $sayacgelen->save();
                                                                    if ($sayacgelen->servis_id == 5) {
                                                                        $sayac = Sayac::where('serino', $serino)->where('sayacadi_id', $sayacadi)->where('sayaccap_id', 1)->where('uretimyer_id', $uretimyeri)
                                                                            ->where('sayactur_id', 5)->first();
                                                                        if (!$sayac) // mekanik sayaç yoksa sayaç listesine eklenecek
                                                                        {
                                                                            $sayac = new Sayac;
                                                                            $sayac->serino = $serino;
                                                                            $sayac->cihazno = $serino;
                                                                            $sayac->sayactur_id = 5;
                                                                            $sayac->sayacadi_id = $sayacadi;
                                                                            $sayac->sayaccap_id = 1;
                                                                            $sayac->uretimyer_id = $uretimyeri;
                                                                            $sayac->save();
                                                                        }
                                                                    }
                                                                    $biten++;
                                                                    $servistakip = new ServisTakip;
                                                                    $servistakip->serino = $sayacgelen->serino;
                                                                    $servistakip->sayacadi_id = $sayacgelen->sayacadi_id;
                                                                    $servistakip->depogelen_id = $sayacgelen->depogelen_id;
                                                                    $servistakip->netsiscari_id = $sayacgelen->netsiscari_id;
                                                                    $servistakip->sayacgelen_id = $sayacgelen->id;
                                                                    $servistakip->servis_id = $sayacgelen->servis_id;
                                                                    $servistakip->uretimyer_id = $sayacgelen->uretimyer_id;
                                                                    $servistakip->durum = 1;
                                                                    $servistakip->subedurum = 1;
                                                                    $servistakip->subekodu=$subekodu;
                                                                    $servistakip->depotarih = $sayacgelen->depotarihi;
                                                                    $servistakip->sayacgiristarihi = $sayacgelen->eklenmetarihi;
                                                                    $servistakip->kullanici_id = $kullanici->id;
                                                                    $servistakip->sonislemtarihi = $sayacgelen->eklenmetarihi;
                                                                    $servistakip->save();
                                                                    $abonesayac = AboneSayac::where('serino', $serino)->first();
                                                                    if ($abonesayac) {
                                                                        $abonetahsis = AboneTahsis::where('abonesayac_id', $abonesayac->id)->first();
                                                                        if ($abonetahsis) {
                                                                            $serviskayit = ServisKayit::where('depogelen_id', $depogelen->id)->where('abonetahsis_id', $abonetahsis->id)->first();
                                                                            if ($serviskayit) {
                                                                                $serviskayit->netsiscari_id = $netsiscari_id;
                                                                                $serviskayit->uretimyer_id = $uretimyeri;
                                                                                $serviskayit->sokulmedurumu = 1;
                                                                                $serviskayit->aciklama = $sayacgelen->sokulmenedeni;
                                                                                $serviskayit->servissayaci = $sayacgelen->takilmatarihi==NULL ? 0 : 1;
                                                                                $serviskayit->takilmatarihi = $sayacgelen->takilmatarihi;
                                                                                $serviskayit->ilkendeks = $sayacgelen->endeks;
                                                                                $serviskayit->save();
                                                                            }
                                                                        }
                                                                    }

                                                                    $depolararasi = Depolararasi::where('servis_id', $servisid)->where('netsiscari_id', $sayacgelen->netsiscari_id)->where('depokodu', $depogelen->depokodu)
                                                                        ->where('tipi', 0)->where('depodurum', 0)->first();
                                                                    if ($depolararasi) {
                                                                        $depolararasi->secilenler .= ($depolararasi->secilenler == "" ? "" : ",") . $sayacgelen->id; //bu bilgiler yok
                                                                        $depolararasi->sayacsayisi += 1;
                                                                    } else {
                                                                        $depolararasi = new Depolararasi;
                                                                        $depolararasi->subekodu = $subekodu;
                                                                        $depolararasi->servis_id = $servisid;
                                                                        $depolararasi->netsiscari_id = $sayacgelen->netsiscari_id;
                                                                        $depolararasi->secilenler = $sayacgelen->id;
                                                                        $depolararasi->sayacsayisi = 1;
                                                                        $depolararasi->depodurum = 0;
                                                                        $depolararasi->depokodu = $depogelen->depokodu;
                                                                    }
                                                                    $depolararasi->save();

                                                                } catch (Exception $e) {
                                                                    Log::error($e);
                                                                    DB::rollBack();
                                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayac Kayıdı Yapılamadı', 'text' => 'Sayac Kayıdı Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                                                }
                                                            }
                                                        } catch (Exception $e) {
                                                            Log::error($e);
                                                            DB::rollBack();
                                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayac Kayıdı Yapılamadı', 'text' => 'Sayac Kayıdı Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                                        }
                                                    }
                                                }
                                                try {
                                                    if ($biten > 0 ) {
                                                        BackendController::HatirlatmaGuncelle(2, $netsiscari_id, $servisid, $biten, $depogelen->id, $depogelen->servisstokkodu);
                                                        BackendController::DepoDurumGuncelle($depogelen->id);
                                                        BackendController::HatirlatmaEkle(3, $netsiscari_id, $servisid, $biten, $depogelen->id, $depogelen->servisstokkodu);
                                                        BackendController::HatirlatmaEkle(11, $netsiscari_id, $servisid, $biten, $depogelen->id, $depogelen->servisstokkodu);
                                                        BackendController::BildirimEkle(2, $netsiscari_id, $servisid, $biten, $depogelen->id, $depogelen->servisstokkodu);
                                                    }
                                                } catch (Exception $e) {
                                                    DB::rollBack();
                                                    Log::error($e);
                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Giriş Bilgisi Güncellenemedi', 'text' => 'Hatırlatma Kayıdı Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                                }
                                            }else{
                                                $biten=0;
                                                $eskiler = SayacGelen::where('depogelen_id', $depogelen->id)->get();
                                                $eskisayaclar = SayacGelen::where('depogelen_id', $depogelen->id)->get();
                                                $eskisayi = $eskisayaclar->count();
                                                foreach ($eskisayaclar as $eskisayac) {
                                                    $eskisayac->flag = 0;
                                                }
                                                $yeniids = array();
                                                $allids = array();
                                                foreach ($sayac as $girilecek) {
                                                    $serino = $girilecek['serino'];
                                                    $sayacadi = $girilecek['sayacadi'];
                                                    $sayaccap = $girilecek['sayaccap'];
                                                    $uretimyeri = $girilecek['uretimyeri'];
                                                    $sokulmenedeni = $girilecek['neden'];
                                                    $takilmatarih = $girilecek['takilmatarihi'];
                                                    $endeks = $girilecek['endeks'];
                                                    if ($serino != "") {
                                                        try {
                                                            $flag = 0;
                                                            for ($j = 0; $j < $eskiler->count(); $j++) {
                                                                $sayacgelen = $eskiler[$j];
                                                                $eskisayac = $eskisayaclar[$j];
                                                                if ($sayacgelen->serino == $serino && $eskisayac->flag == 0) {
                                                                    try {
                                                                        $flag = 1;
                                                                        $eskisayac->flag = 1;
                                                                        $sayacgelen->depogelen_id = $depogelen->id;
                                                                        $sayacgelen->netsiscari_id = $netsiscari_id;
                                                                        $sayacgelen->stokkodu = $depogelen->servisstokkodu;
                                                                        $sayacgelen->serino = $serino;
                                                                        $sayacgelen->depotarihi = $depogelen->tarih;
                                                                        $sayacgelen->sayacadi_id = $sayacadi;
                                                                        $sayacgelen->sayaccap_id = $sayaccap;
                                                                        $sayacgelen->uretimyer_id = $uretimyeri;
                                                                        $sayacgelen->kullanici_id = Auth::user()->id;
                                                                        $sayacgelen->sokulmenedeni = $sokulmenedeni;
                                                                        $sayacgelen->takilmatarihi = $takilmatarih=="" ? NULL : date("Y-m-d", strtotime($takilmatarih));
                                                                        $sayacgelen->endeks = $endeks;
                                                                        $sayacgelen->save();
                                                                        if ($sayacgelen->servis_id == 5) {
                                                                            $sayac = Sayac::where('serino', $serino)->where('sayactur_id', 5)->where('uretimyer_id', $uretimyeri)->first();
                                                                            if ($sayac) {
                                                                                $sayac->sayacadi_id = $sayacadi;
                                                                                $sayac->sayaccap_id = $sayaccap;
                                                                                $sayac->uretimyer_id = $uretimyeri;
                                                                                $sayac->save();
                                                                            } else {
                                                                                $sayac = new Sayac;
                                                                                $sayac->serino = $serino;
                                                                                $sayac->cihazno = $serino;
                                                                                $sayac->sayactur_id = 5;
                                                                                $sayac->sayacadi_id = $sayacadi;
                                                                                $sayac->sayaccap_id = 1;
                                                                                $sayac->uretimyer_id = $uretimyeri;
                                                                                $sayac->save();
                                                                            }
                                                                        }
                                                                        $biten++;
                                                                        array_push($allids, $sayacgelen->id);
                                                                        $servistakip = ServisTakip::where('sayacgelen_id', $sayacgelen->id)->first();
                                                                        if ($servistakip) {
                                                                            $servistakip->depogelen_id = $depogelen->id;
                                                                            $servistakip->serino = $serino;
                                                                            $servistakip->sayacadi_id = $sayacadi;
                                                                            $servistakip->netsiscari_id = $netsiscari_id;
                                                                            $servistakip->uretimyer_id = $uretimyeri;
                                                                            $servistakip->save();
                                                                        }
                                                                        $abonesayac = AboneSayac::where('serino', $serino)->first();
                                                                        if ($abonesayac) {
                                                                            $abonetahsis = AboneTahsis::where('abonesayac_id', $abonesayac->id)->first();
                                                                            if ($abonetahsis) {
                                                                                $serviskayit = ServisKayit::where('depogelen_id', $depogelen->id)->where('abonetahsis_id', $abonetahsis->id)->first();
                                                                                if ($serviskayit) {
                                                                                    $serviskayit->netsiscari_id = $netsiscari_id;
                                                                                    $serviskayit->uretimyer_id = $uretimyeri;
                                                                                    $serviskayit->sokulmedurumu = 1;
                                                                                    $serviskayit->aciklama = $sayacgelen->sokulmenedeni;
                                                                                    $serviskayit->servissayaci = $sayacgelen->takilmatarihi==NULL ? 0 : 1;
                                                                                    $serviskayit->takilmatarihi = $sayacgelen->takilmatarihi;
                                                                                    $serviskayit->ilkendeks = $sayacgelen->endeks;
                                                                                    $serviskayit->save();
                                                                                }
                                                                            }
                                                                        }
                                                                        if($eskisayac->netsiscari_id!=$netsiscari_id){
                                                                            $depolararasi = Depolararasi::where('netsiscari_id', $eskisayac->netsiscari_id)->where('secilenler', 'LIKE', '%' . $sayacgelen->id . '%')
                                                                                ->where('servis_id', 6)->where('tipi', 0)->where('depodurum', 0)->get();
                                                                            foreach ($depolararasi as $depoislem) {
                                                                                $secilenlist = explode(',', $depoislem->secilenler);
                                                                                if (in_array($sayacgelen->id, $secilenlist)) { //sayaç bu listedeyse
                                                                                    if(count($secilenlist)>1){ // sayaç bu listeden ayrılacak
                                                                                        $secilenler = "";
                                                                                        foreach ($secilenlist as $secilen) {
                                                                                            if ($secilen != $sayacgelen->id)
                                                                                                $secilenler = ($secilenler == "" ? "" : ",") . $secilen;
                                                                                        }
                                                                                        $depoislem->secilenler = $secilenler;
                                                                                        $depoislem->save();

                                                                                        $yenidepoislem = Depolararasi::where('servis_id', $servisid)->where('netsiscari_id', $netsiscari_id)->where('depokodu', $depogelen->depokodu)
                                                                                            ->where('tipi', 0)->where('depodurum', 0)->first();
                                                                                        if ($yenidepoislem) {
                                                                                            $yenidepoislem->secilenler .= ($yenidepoislem->secilenler == "" ? "" : ",") . $sayacgelen->id; //bu bilgiler yok
                                                                                            $yenidepoislem->sayacsayisi += 1;
                                                                                        } else {
                                                                                            $yenidepoislem = new Depolararasi;
                                                                                            $yenidepoislem->subekodu = $subekodu;
                                                                                            $yenidepoislem->servis_id = $servisid;
                                                                                            $yenidepoislem->netsiscari_id = $sayacgelen->netsiscari_id;
                                                                                            $yenidepoislem->secilenler = $sayacgelen->id;
                                                                                            $yenidepoislem->sayacsayisi = 1;
                                                                                            $yenidepoislem->depodurum = 0;
                                                                                            $yenidepoislem->depokodu = $depogelen->depokodu;
                                                                                            $yenidepoislem->save();
                                                                                        }
                                                                                    }else{
                                                                                        $depoislem->netsiscari_id = $sayacgelen->netsiscari_id;
                                                                                        $depoislem->secilenler = $sayacgelen->id;
                                                                                        $depoislem->sayacsayisi = 1;
                                                                                        $depoislem->depodurum = 0;
                                                                                        $depoislem->depokodu = $depogelen->depokodu;
                                                                                        $depoislem->save();
                                                                                    }
                                                                                    break;
                                                                                }
                                                                            }
                                                                        }
                                                                        break;
                                                                    } catch (Exception $e) {
                                                                        Log::error($e);
                                                                        DB::rollBack();
                                                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo kayıdı Güncellenemedi', 'text' => 'Sayac Kayıdı Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                                                    }
                                                                } else {
                                                                    continue;
                                                                }
                                                            }
                                                            if ($flag == 0) { //yeni eklemeyse
                                                                try {
                                                                    $sayacgelen = new SayacGelen;
                                                                    $sayacgelen->depogelen_id = $depogelen->id;
                                                                    $sayacgelen->netsiscari_id = $netsiscari_id;
                                                                    $sayacgelen->stokkodu = $depogelen->servisstokkodu;
                                                                    $sayacgelen->serino = $serino;
                                                                    $sayacgelen->depotarihi = $depogelen->tarih;
                                                                    $sayacgelen->sayacadi_id = $sayacadi;
                                                                    $sayacgelen->sayaccap_id = $sayaccap;
                                                                    $sayacgelen->uretimyer_id = $uretimyeri;
                                                                    $sayacgelen->servis_id = $servisid;
                                                                    $sayacgelen->subekodu=$subekodu;
                                                                    $sayacgelen->kullanici_id = $kullanici->id;
                                                                    $sayacgelen->beyanname = -2;
                                                                    $sayacgelen->sokulmenedeni = $sokulmenedeni;
                                                                    $sayacgelen->takilmatarihi = $takilmatarih=="" ? NULL : date("Y-m-d", strtotime($takilmatarih));
                                                                    $sayacgelen->endeks = $endeks;
                                                                    $sayacgelen->save();
                                                                    array_push($yeniids, $sayacgelen->id);
                                                                    array_push($allids, $sayacgelen->id);
                                                                    if ($sayacgelen->servis_id == 5) {
                                                                        $sayac = Sayac::where('serino', $serino)->where('sayacadi_id', $sayacadi)->where('sayaccap_id', 1)->where('uretimyer_id', $uretimyeri)
                                                                            ->where('sayactur_id', 5)->first();
                                                                        if (!$sayac) // mekanik sayaç yoksa sayaç listesine eklenecek
                                                                        {
                                                                            $sayac = new Sayac;
                                                                            $sayac->serino = $serino;
                                                                            $sayac->cihazno = $serino;
                                                                            $sayac->sayactur_id = 5;
                                                                            $sayac->sayacadi_id = $sayacadi;
                                                                            $sayac->sayaccap_id = 1;
                                                                            $sayac->uretimyer_id = $uretimyeri;
                                                                            $sayac->save();
                                                                        }
                                                                    }
                                                                    $biten++;
                                                                    $servistakip = new ServisTakip;
                                                                    $servistakip->serino = $sayacgelen->serino;
                                                                    $servistakip->sayacadi_id = $sayacgelen->sayacadi_id;
                                                                    $servistakip->depogelen_id = $sayacgelen->depogelen_id;
                                                                    $servistakip->netsiscari_id = $sayacgelen->netsiscari_id;
                                                                    $servistakip->sayacgelen_id = $sayacgelen->id;
                                                                    $servistakip->servis_id = $sayacgelen->servis_id;
                                                                    $servistakip->uretimyer_id = $sayacgelen->uretimyer_id;
                                                                    $servistakip->durum = 1;
                                                                    $servistakip->subedurum = 1;
                                                                    $servistakip->subekodu=$subekodu;
                                                                    $servistakip->depotarih = $sayacgelen->depotarihi;
                                                                    $servistakip->sayacgiristarihi = $sayacgelen->eklenmetarihi;
                                                                    $servistakip->kullanici_id = $kullanici->id;
                                                                    $servistakip->sonislemtarihi = $sayacgelen->eklenmetarihi;
                                                                    $servistakip->save();
                                                                    $abonesayac = AboneSayac::where('serino', $serino)->first();
                                                                    if ($abonesayac) {
                                                                        $abonetahsis = AboneTahsis::where('abonesayac_id', $abonesayac->id)->first();
                                                                        if ($abonetahsis) {
                                                                            $abone = Abone::find($abonetahsis->abone_id);
                                                                            $serviskayit = ServisKayit::where('depogelen_id', $depogelen->id)->where('abonetahsis_id', $abonetahsis->id)->first();
                                                                            if ($serviskayit) {
                                                                                $serviskayit->netsiscari_id = $netsiscari_id;
                                                                                $serviskayit->uretimyer_id = $uretimyeri;
                                                                                $serviskayit->sokulmedurumu = 1;
                                                                                $serviskayit->aciklama = $sayacgelen->sokulmenedeni;
                                                                                $serviskayit->servissayaci = $sayacgelen->takilmatarihi == NULL ? 0 : 1;
                                                                                $serviskayit->takilmatarihi = $sayacgelen->takilmatarihi;
                                                                                $serviskayit->ilkendeks = $sayacgelen->endeks;
                                                                                $serviskayit->save();
                                                                            }
                                                                            $depolararasi = Depolararasi::where('servis_id', $servisid)->where('netsiscari_id', $abone->netsiscari_id)->where('depokodu', $depogelen->depokodu)
                                                                                ->where('tipi', 0)->where('depodurum', 0)->first();
                                                                            if ($depolararasi) {
                                                                                $depolararasi->secilenler .= ($depolararasi->secilenler == "" ? "" : ",") . $sayacgelen->id; //bu bilgiler yok
                                                                                $depolararasi->sayacsayisi += 1;
                                                                            } else {
                                                                                $depolararasi = new Depolararasi;
                                                                                $depolararasi->subekodu = $abone->subekodu;
                                                                                $depolararasi->servis_id = $this->servisid;
                                                                                $depolararasi->netsiscari_id = $abone->netsiscari_id;
                                                                                $depolararasi->secilenler = $sayacgelen->id;
                                                                                $depolararasi->sayacsayisi = 1;
                                                                                $depolararasi->depodurum = 0;
                                                                                $depolararasi->depokodu = $depogelen->depokodu;
                                                                            }
                                                                            $depolararasi->save();
                                                                        }
                                                                    }
                                                                } catch (Exception $e) {
                                                                    Log::error($e);
                                                                    DB::rollBack();
                                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayac Kayıdı Yapılamadı', 'text' => 'Sayac Kayıdı Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                                                }
                                                            }
                                                        } catch (Exception $e) {
                                                            Log::error($e);
                                                            DB::rollBack();
                                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayac Kayıdı Yapılamadı', 'text' => 'Sayac Kayıdı Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                                        }
                                                    }
                                                }
                                                for ($j = 0; $j < $eskisayaclar->count(); $j++) {
                                                    $sayacgelen = $eskisayaclar[$j];
                                                    if ($sayacgelen->flag == 0) { // silinecek varsa
                                                        try {
                                                            $sayacgelen = SayacGelen::find($sayacgelen->id);
                                                            $servistakip = ServisTakip::where('sayacgelen_id', $sayacgelen->id)->first();
                                                            $depolararasi = Depolararasi::where('netsiscari_id', $netsiscari_id)->where('secilenler', 'LIKE', '%' . $sayacgelen->id . '%')
                                                                ->where('servis_id', 6)->where('tipi', 0)->where('depodurum', 0)->get();
                                                            BackendController::HatirlatmaSil(3, $netsiscari_id, $servisid, 1, $depogelen->id, $depogelen->servisstokkodu);
                                                            BackendController::HatirlatmaSil(11, $netsiscari_id, $servisid, 1, $depogelen->id, $depogelen->servisstokkodu);
                                                            BackendController::BildirimGeriAl(2, $netsiscari_id, $servisid, 1, $depogelen->id, $depogelen->servisstokkodu);
                                                            foreach ($depolararasi as $depoislem) {
                                                                $secilenlist = explode(',', $depoislem->secilenler);
                                                                if (in_array($sayacgelen->id, $secilenlist)) { //sayaç bu listedeyse
                                                                    $secilenler = "";
                                                                    foreach ($secilenlist as $secilen) {
                                                                        if ($secilen != $sayacgelen->id)
                                                                            $secilenler = ($secilenler == "" ? "" : ",") . $secilen;
                                                                    }
                                                                    if ($secilenler != "") {
                                                                        $depoislem->secilenler = $secilenler;
                                                                        $depoislem->save();
                                                                    } else {
                                                                        $depoislem->delete();
                                                                    }
                                                                    break;
                                                                }
                                                            }
                                                            $abonesayac = AboneSayac::where('serino', $sayacgelen->serino)->first();
                                                            if ($abonesayac) {
                                                                $abonetahsis = AboneTahsis::where('abonesayac_id', $abonesayac->id)->first();
                                                                if ($abonetahsis) {
                                                                    $serviskayit = ServisKayit::where('depogelen_id', $depogelen->id)->where('abonetahsis_id', $abonetahsis->id)->first();
                                                                    if ($serviskayit) {
                                                                        $serviskayit->depogelen_id = null;
                                                                        $serviskayit->sokulmedurumu = 0;
                                                                        $serviskayit->aciklama = '';
                                                                        $serviskayit->servissayaci = $sayacgelen->takilmatarihi==NULL ? 0 : 1;
                                                                        $serviskayit->takilmatarihi = $sayacgelen->takilmatarihi;
                                                                        $serviskayit->ilkendeks = $sayacgelen->endeks;
                                                                        $serviskayit->save();
                                                                    }
                                                                }
                                                            }
                                                            $servistakip->delete();
                                                            $sayacgelen->delete();
                                                        } catch (Exception $e) {
                                                            Log::error($e);
                                                            DB::rollBack();
                                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Eski Sayaç Kayıdı Silinemedi', 'text' => 'Eski Sayaç Kayıdı Silinirken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                                        }
                                                    }
                                                }
                                                try {
                                                    if ($biten > 0 && $biten != $eskisayi) {
                                                        $sayi = $biten - $eskisayi; //silinme durumu yukarda çözüldü ekleme varsa eklenecek
                                                        if ($sayi > 0) {
                                                            BackendController::HatirlatmaGuncelle(2, $netsiscari_id, $servisid, $sayi, $depogelen->id, $depogelen->servisstokkodu);
                                                            BackendController::DepoDurumGuncelle($depogelen->id);
                                                            BackendController::HatirlatmaEkle(3, $netsiscari_id, $servisid, $sayi, $depogelen->id, $depogelen->servisstokkodu);
                                                            BackendController::HatirlatmaEkle(11, $netsiscari_id, $servisid, $sayi, $depogelen->id, $depogelen->servisstokkodu);
                                                            BackendController::BildirimEkle(2, $netsiscari_id, $servisid, $sayi, $depogelen->id, $depogelen->servisstokkodu);
                                                        }
                                                    }
                                                } catch (Exception $e) {
                                                    DB::rollBack();
                                                    Log::error($e);
                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Giriş Bilgisi Güncellenemedi', 'text' => 'Hatırlatma Kayıdı Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    DB::rollBack();
                                    Input::flash();
                                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Depo Giriş Bilgisi Güncellenemedi', 'text' => 'Depo Gelen Bilgisi Kaydedilmemiş', 'type' => 'error'));
                                }
                            }
                        }
                    } catch (Exception $e) {
                        DB::rollBack();
                        Log::error($e);
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Giriş Bilgisi Güncellenemedi', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
                    }
                    BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-thumbs-o-up', $depogelen->id . ' Nolu Depo Girişi Güncellendi.', 'Güncelleme Yapan:' . $kullanici->adi_soyadi . ',Depo Giriş Numarası:' . $depogelen->id);
                    DB::commit();
                    return Redirect::to('depo/depogelen')->with(array('mesaj' => 'true', 'title' => 'Depo Giriş Bilgisi Güncellendi', 'text' => 'Depo Giriş Bilgisi Başarıyla Güncellendi', 'type' => 'success'));
                }else{
                    Input::flash();
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Ambar Girişi Kaydedilemedi', 'text' => 'Girilen Sayaçların Seri Numaraları yazılmamış', 'type' => 'error'));
                }
            }else{
                $servistakipler = ServisTakip::where('depogelen_id', $id)->get();
                $sube = Sube::where('netsiscari_id', $netsiscari_id)->where('aktif', 1)->first();
                $subedurum = 0;
                if($sube){
                    foreach ($servistakipler as $servistakip){
                        if($servistakip->subedurum)
                            $subedurum = 1;
                    }
                }
                if (count($serinolar) > 0) {
                    for ($i = 0; $i < count($serinolar); $i++) {
                        $serino1 = $serinolar[$i];
                        $sayacadi=$sayacadlari[$i];
                        $yeri = $uretimyerleri[$i];
                        if ($serino1 == "")
                            continue;
                        $servistakip = ServisTakip::where('serino', $serino1)->where('depogelen_id', $depogelen->id)->first();
                        if ($servistakip) {
                            if($servistakip->arizakayit_id==null)
                                if(BackendController::SayacDurum($serino1,$yeri,$servisid,$subedurum, $servistakip->id,$sayacadi)) {
                                    Input::flash();
                                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Seri No Hatası', 'text' => $serino1 . ' Nolu Sayacın Depo Çıkışı Yapılmamış.Önce Sayaca Ait Diğer İşlemleri Bitiriniz.', 'type' => 'error'));
                                }
                        }
                        if(isset($uretimyerleri[$i])){
                            if($uretimyerleri[$i]==""){
                                Input::flash();
                                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Geliş Yeri Hatası', 'text' => 'Seri Numarası Girilen Sayacın Geliş Yeri Boş Geçilmiş!', 'type' => 'error'));
                            }
                        }
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
                        if(isset($serviskodlari[$i])){
                            if($serviskodlari[$i]==""){
                                Input::flash();
                                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Servis Kodu Hatası', 'text' => 'Seri Numarası Girilen Sayacın İstek Kısmı Boş Geçilmiş!', 'type' => 'error'));
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
                    $kullanici = Kullanici::find(Auth::user()->id);
                    if ($netsiscari->caridurum != "A") {
                        Input::flash();
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Netsis Cari Uyarısı', 'text' => 'Cari Bilgisi Netsis Üzerinde Kilitli.', 'type' => 'warning'));
                    }
                    $sayaclar = BackendController::DepoGirisGrupla($serinolar, $uretimyerleri, $serviskodlari, $sayacadlari, $sayaccaplari,$uretimyillari);
                    if (count($sayaclar) == 0) {
                        Input::flash();
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Ambar Girişi Kaydedilemedi', 'text' => 'Girilen Sayaçların Seri Numaraları yazılmamış', 'type' => 'error'));
                    }
                    DB::beginTransaction();
                    $depogiris = BackendController::NetsisDepoGirisDuzenle($dbname,$sayaclar,$gelistarih,$netsiscari_id,$belgeno);
                    //Config::set('database.connections.sqlsrv2.database', 'MANAS' . date('Y'));
                    if ($depogiris['durum'] == '0') {
                        DB::rollBack();
                        Input::flash();
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Ambar Girişi Kaydedilemedi', 'text' => $depogiris['text'], 'type' => 'error'));
                    }
                    $inckeys = $depogiris['faturakalemler'];
                    $dbnames = $depogiris['dbnames'];
                    if ($dbname != 'MANAS' . date('Y')) { //eski depo girişi guncelleniyorsa
                        $dbname = 'MANAS'.date('Y');
                    }
                    try {
                        for ($i=0;$i<count($inckeys);$i++) {
                            if ($dbname != $dbnames[$i]) { //eski depo girişi
                                $depogelen = DepoGelen::where('db_name', $dbnames[$i])->where('inckeyno', $inckeys[$i])->first();
                                if ($depogelen) {
                                    // eksilen depo kaydı
                                    // düzenlenen depo kaydı
                                    // yeni yok
                                    $depogelen->kod = ServisStokKod::where('stokkodu', $depogelen->servisstokkodu)->first();
                                    foreach ($sayaclar as $sayacgrup) {
                                        if ($sayacgrup['kod'] == $depogelen->kod->id) {
                                            $sayac = $sayacgrup['sayac']; //eklenecek sayaçlar
                                            $eskiler = SayacGelen::where('depogelen_id', $depogelen->id)->get();
                                            $eskisayaclar = SayacGelen::where('depogelen_id', $depogelen->id)->get();
                                            foreach ($eskisayaclar as $eskisayac) {
                                                $eskisayac->flag = 0;
                                            }
                                            foreach ($sayac as $girilecek) {
                                                $serino = $girilecek['serino'];
                                                $sayacadi = $girilecek['sayacadi'];
                                                $sayaccap = $girilecek['sayaccap'];
                                                $uretimyeri = $girilecek['uretimyeri'];
                                                $uretimtarihi = $girilecek['uretimyili'];
                                                if ($serino != "") {
                                                    try {
                                                        for ($j = 0; $j < $eskiler->count(); $j++) {
                                                            $sayacgelen = $eskiler[$j];
                                                            $eskisayac = $eskisayaclar[$j];
                                                            if ($sayacgelen->serino == $serino && $eskisayac->flag == 0) {
                                                                try {
                                                                    $eskisayac->flag = 1;
                                                                    if(!$sayacgelen->arizakayit){
                                                                        $sayacgelen->depogelen_id = $depogelen->id;
                                                                        $sayacgelen->netsiscari_id = $netsiscari_id;
                                                                        $sayacgelen->stokkodu = $depogelen->servisstokkodu;
                                                                        $sayacgelen->serino = $serino;
                                                                        $sayacgelen->depotarihi = $depogelen->tarih;
                                                                        $sayacgelen->sayacadi_id = $sayacadi;
                                                                        $sayacgelen->sayaccap_id = $sayaccap;
                                                                        $sayacgelen->uretimyer_id = $uretimyeri;
                                                                        $sayacgelen->kullanici_id = Auth::user()->id;
                                                                        $sayacgelen->save();
                                                                        if ($sayacgelen->servis_id == 5) {
                                                                            $sayac = Sayac::where('serino', $serino)->where('sayactur_id', 5)->where('uretimyer_id', $uretimyeri)->first();
                                                                            if ($sayac) {
                                                                                $sayac->sayacadi_id = $sayacadi;
                                                                                $sayac->sayaccap_id = $sayaccap;
                                                                                if ($uretimtarihi != "") {
                                                                                    $utarih = date('Y-m-d', mktime(0, 0, 0, 1, 1, $uretimtarihi));
                                                                                    $sayac->uretimtarihi = $utarih;
                                                                                }
                                                                                $sayac->uretimyer_id = $uretimyeri;
                                                                                $sayac->save();
                                                                            } else {
                                                                                $sayac = new Sayac;
                                                                                $sayac->serino = $serino;
                                                                                $sayac->cihazno = $serino;
                                                                                $sayac->sayactur_id = 5;
                                                                                $sayac->sayacadi_id = $sayacadi;
                                                                                $sayac->sayaccap_id = 1;
                                                                                if ($uretimtarihi != "") {
                                                                                    $utarih = date('Y-m-d', mktime(0, 0, 0, 1, 1, $uretimtarihi));
                                                                                    $sayac->uretimtarihi = $utarih;
                                                                                }
                                                                                $sayac->uretimyer_id = $uretimyeri;
                                                                                $sayac->save();
                                                                            }
                                                                        }
                                                                        $servistakip = ServisTakip::where('sayacgelen_id', $sayacgelen->id)->first();
                                                                        if ($servistakip) {
                                                                            $servistakip->depogelen_id = $depogelen->id;
                                                                            $servistakip->serino = $serino;
                                                                            $servistakip->sayacadi_id = $sayacadi;
                                                                            $servistakip->netsiscari_id = $netsiscari_id;
                                                                            $servistakip->uretimyer_id = $uretimyeri;
                                                                            $servistakip->save();

                                                                            if($servistakip->abonesayackayitbilgi_id != null){
                                                                                $abonesayackayitbilgi = AboneSayacKayitBilgi::find($servistakip->abonesayackayitbilgi_id);
                                                                                if(!$abonesayackayitbilgi){
                                                                                    Input::flash();
                                                                                    DB::rollBack();
                                                                                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Müşteri Kayıdı Hatalı', 'text' => ' Müşteri Kayıdı Bulunamadı!', 'type' => 'error'));
                                                                                }
                                                                                if($serino != $abonesayackayitbilgi->serino){
                                                                                    $abonesayackayit = AboneSayacKayit::find($abonesayackayitbilgi->abonesayackayit_id);
                                                                                    $eklisayackayitbilgi = AboneSayacKayitBilgi::where('abonesayackayit_id',$abonesayackayitbilgi->abonesayackayit_id)->where('serino',$serino)->where('id','<>',$servistakip->abonesayackayitbilgi_id)->first();
                                                                                    if($eklisayackayitbilgi){
                                                                                        if($eklisayackayitbilgi->durum==0){
                                                                                            $eklisayackayitbilgi->durum = 1;
                                                                                            $eklisayackayitbilgi->save();
                                                                                            $abonesayackayitbilgi->durum = 0;
                                                                                            $abonesayackayitbilgi->save();
                                                                                            $servistakip->abonesayackayitbilgi_id=$eklisayackayitbilgi->id;
                                                                                            $servistakip->save();
                                                                                        }else{
                                                                                            Input::flash();
                                                                                            DB::rollBack();
                                                                                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Müşteri Kayıdı Hatalı', 'text' => $serino . ' Nolu Sayaç için zaten kayıt mevcut!', 'type' => 'error'));
                                                                                        }
                                                                                    }else{
                                                                                        $abonesayackayitbilgi->durum=0;
                                                                                        $abonesayackayitbilgi->save();
                                                                                        $abonesayackayit->durum = 2;
                                                                                        $abonesayackayit->save();
                                                                                        $servistakip->abonesayackayitbilgi_id=null;
                                                                                        $servistakip->save();
                                                                                    }
                                                                                }
                                                                            }else{
                                                                                $bilgi = AboneSayacKayit::where('abonesayackayit.netsiscari_id',$netsiscari->id)->where('abonesayackayit.durum',2)
                                                                                    ->where('abonesayackayitbilgi.serino',$serino)
                                                                                    ->leftJoin('abonesayackayitbilgi','abonesayackayitbilgi.abonesayackayit_id','=','abonesayackayit.id')->first(array('abonesayackayitbilgi.id'));
                                                                                if($bilgi){
                                                                                    $abonesayackayitbilgi = AboneSayacKayitBilgi::find($bilgi->id);
                                                                                    $abonesayackayitbilgi->durum = 1;
                                                                                    $abonesayackayitbilgi->save();
                                                                                    $servistakip->abonesayackayitbilgi_id=$abonesayackayitbilgi->id;
                                                                                    $servistakip->save();
                                                                                    $abonesayackayit = AboneSayacKayit::find($abonesayackayitbilgi->abonesayackayit_id);
                                                                                    $hatalikayitlar = AboneSayacKayitBilgi::where('abonesayackayit_id',$abonesayackayit->id)->where('durum',0)->get();
                                                                                    if($hatalikayitlar->count()>0){
                                                                                        $abonesayackayit->durum=2;
                                                                                        $abonesayackayit->save();
                                                                                    }else{
                                                                                        $abonesayackayit->durum=1;
                                                                                        $abonesayackayit->save();
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                        array_push($allids, $sayacgelen->id);
                                                                    }
                                                                    break;
                                                                } catch (Exception $e) {
                                                                    Log::error($e);
                                                                    DB::rollBack();
                                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo kayıdı Güncellenemedi', 'text' => 'Sayac Kayıdı Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                                                }
                                                            } else {
                                                                continue;
                                                            }
                                                        }
                                                    } catch (Exception $e) {
                                                        Log::error($e);
                                                        DB::rollBack();
                                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayac Kayıdı Yapılamadı', 'text' => 'Sayac Kayıdı Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                                    }
                                                }
                                            }
                                            for ($j = 0; $j < $eskisayaclar->count(); $j++) {
                                                $sayacgelen = $eskisayaclar[$j];
                                                if ($sayacgelen->flag == 0) { // silinecek varsa
                                                    try {
                                                        $sayacgelen = SayacGelen::find($sayacgelen->id);
                                                        $servistakip = ServisTakip::where('sayacgelen_id', $sayacgelen->id)->first();
                                                        if ($sayacgelen->servis_id == 5) {
                                                            $sayac = Sayac::where('serino', $sayacgelen->serino)->where('uretimyer_id', $sayacgelen->uretimyeri_id)
                                                                ->where('sayactur_id', 5)->first();
                                                            if ($sayac) { // mekanik sayaç varsa ve başka kayıdı yoksa silinecek
                                                                $eskisayacgelen = SayacGelen::where('serino', $sayacgelen->serino)->where('sayactur_id', 5)->where('id', '<>', $sayacgelen->id)->first();
                                                                if (!$eskisayacgelen)
                                                                    $sayac->delete();
                                                            }
                                                        }
                                                        if($servistakip->abonesayackayitbilgi_id != null){
                                                            $abonesayackayitbilgi = AboneSayacKayitBilgi::find($servistakip->abonesayackayitbilgi_id);
                                                            if(!$abonesayackayitbilgi){
                                                                Input::flash();
                                                                DB::rollBack();
                                                                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Müşteri Kayıdı Hatalı', 'text' => ' Müşteri Kayıdı Bulunamadı!', 'type' => 'error'));
                                                            }
                                                            $abonesayackayit = AboneSayacKayit::find($abonesayackayitbilgi->abonesayackayit_id);
                                                            $abonesayackayitbilgi->durum=0;
                                                            $abonesayackayitbilgi->save();
                                                            $abonesayackayit->durum = 2;
                                                            $abonesayackayit->save();
                                                            $servistakip->abonesayackayitbilgi_id=null;
                                                            $servistakip->save();
                                                        }
                                                        BackendController::HatirlatmaSil(3, $netsiscari_id, $servisid, 1, $depogelen->id, $depogelen->servisstokkodu);
                                                        BackendController::BildirimGeriAl(2, $netsiscari_id, $servisid, 1, $depogelen->id, $depogelen->servisstokkodu);
                                                        $servistakip->delete();
                                                        $sayacgelen->delete();
                                                    } catch (Exception $e) {
                                                        Log::error($e);
                                                        DB::rollBack();
                                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Eski Sayaç Kayıdı Silinemedi', 'text' => 'Eski Sayaç Kayıdı Silinirken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }else{
                                    DB::rollBack();
                                    Input::flash();
                                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Depo Giriş Bilgisi Güncellenemedi', 'text' => 'Depo Gelen Bilgisi Kaydedilmemiş', 'type' => 'error'));
                                }
                            } else {
                                $depogelen = DepoGelen::where('db_name', $dbname)->where('inckeyno', $inckeys[$i])->first();
                                $now = time();
                                while ($now + 30 > time()) {
                                    $depogelen = DepoGelen::where('db_name', $dbname)->where('inckeyno', $inckeys[$i])->first();
                                    if ($depogelen)
                                        break;
                                }
                                if ($depogelen) {
                                    $hatirlatmalar = Hatirlatma::where('depogelen_id', $depogelen->id)->get();
                                    foreach ($hatirlatmalar as $hatirlatma) {
                                        $hatirlatma->netsiscari_id = $netsiscari->id;
                                        $hatirlatma->save();
                                    }
                                    $depogelen->kod = ServisStokKod::where('stokkodu', $depogelen->servisstokkodu)->first();
                                    foreach ($sayaclar as $sayacgrup) {
                                        if ($sayacgrup['kod'] == $depogelen->kod->id) {
                                            $sayac = $sayacgrup['sayac']; //eklenecek sayaçlar
                                            if($eskidbname!=$dbname){ //eski tarihli sayaçlar için işlem olmayacak
                                                $biten = 0;
                                                $eskidepogelen = SayacGelen::where('netsiscari_id',$netsiscari_id)->where('depotarihi',$gelistarih)
                                                    ->where('stokkodu',$depogelen->servisstokkodu)->where('serino',$sayac[0]['serino'])->first(array('depogelen_id'));
                                                $eskiler = SayacGelen::where('depogelen_id', $eskidepogelen->depogelen_id)->get();
                                                $eskisayaclar = SayacGelen::where('depogelen_id', $eskidepogelen->depogelen_id)->get();
                                                foreach ($sayac as $girilecek) {
                                                    $serino = $girilecek['serino'];
                                                    $sayacadi = $girilecek['sayacadi'];
                                                    $sayaccap = $girilecek['sayaccap'];
                                                    $uretimyeri = $girilecek['uretimyeri'];
                                                    $uretimtarihi = $girilecek['uretimyili'];
                                                    if ($serino != "") {
                                                        try {
                                                            $flag = 0;
                                                            for ($j = 0; $j < $eskiler->count(); $j++) {
                                                                $sayacgelen = $eskiler[$j];
                                                                $eskisayac = $eskisayaclar[$j];
                                                                if ($sayacgelen->serino == $serino) {
                                                                    $flag = 1;
                                                                    $eskisayac->flag = 1;
                                                                    break;
                                                                } else {
                                                                    continue;
                                                                }
                                                            }
                                                            if ($flag == 0) { //yeni eklemeyse
                                                                try {
                                                                    $sayacgelen = new SayacGelen;
                                                                    $sayacgelen->depogelen_id = $depogelen->id;
                                                                    $sayacgelen->netsiscari_id = $netsiscari_id;
                                                                    $sayacgelen->stokkodu = $depogelen->servisstokkodu;
                                                                    $sayacgelen->serino = $serino;
                                                                    $sayacgelen->depotarihi = $depogelen->tarih;;
                                                                    $sayacgelen->sayacadi_id = $sayacadi;
                                                                    $sayacgelen->sayaccap_id = $sayaccap;
                                                                    $sayacgelen->uretimyer_id = $uretimyeri;
                                                                    $sayacgelen->servis_id = $servisid;
                                                                    $sayacgelen->kullanici_id = $kullanici->id;
                                                                    $sayacgelen->beyanname = -2;
                                                                    $sayacgelen->save();
                                                                    if ($sayacgelen->servis_id == 5) {
                                                                        $sayac = Sayac::where('serino', $serino)->where('sayacadi_id', $sayacadi)->where('sayaccap_id', 1)->where('uretimyer_id', $uretimyeri)
                                                                            ->where('sayactur_id', 5)->first();
                                                                        if (!$sayac) // mekanik sayaç yoksa sayaç listesine eklenecek
                                                                        {
                                                                            $sayac = new Sayac;
                                                                            $sayac->serino = $serino;
                                                                            $sayac->cihazno = $serino;
                                                                            $sayac->sayactur_id = 5;
                                                                            $sayac->sayacadi_id = $sayacadi;
                                                                            $sayac->sayaccap_id = 1;
                                                                            if ($uretimtarihi != "") {
                                                                                $utarih = date('Y-m-d', mktime(0, 0, 0, 1, 1, $uretimtarihi));
                                                                                $sayac->uretimtarihi = $utarih;
                                                                            }
                                                                            $sayac->uretimyer_id = $uretimyeri;
                                                                            $sayac->save();
                                                                        }
                                                                    }
                                                                    $biten++;
                                                                    $servistakip = new ServisTakip;
                                                                    $servistakip->serino = $sayacgelen->serino;
                                                                    $servistakip->sayacadi_id = $sayacgelen->sayacadi_id;
                                                                    $servistakip->depogelen_id = $sayacgelen->depogelen_id;
                                                                    $servistakip->netsiscari_id = $sayacgelen->netsiscari_id;
                                                                    $servistakip->sayacgelen_id = $sayacgelen->id;
                                                                    $servistakip->servis_id = $sayacgelen->servis_id;
                                                                    $servistakip->uretimyer_id = $sayacgelen->uretimyer_id;
                                                                    $servistakip->durum = 1;
                                                                    $servistakip->depotarih = $sayacgelen->depotarihi;
                                                                    $servistakip->sayacgiristarihi = $sayacgelen->eklenmetarihi;
                                                                    $servistakip->kullanici_id = $kullanici->id;
                                                                    $servistakip->sonislemtarihi = $sayacgelen->eklenmetarihi;
                                                                    $servistakip->save();

                                                                    $bilgi = AboneSayacKayit::where('abonesayackayit.netsiscari_id',$netsiscari->id)->where('abonesayackayit.durum',2)
                                                                        ->where('abonesayackayitbilgi.serino',$serino)
                                                                        ->leftJoin('abonesayackayitbilgi','abonesayackayitbilgi.abonesayackayit_id','=','abonesayackayit.id')->first(array('abonesayackayitbilgi.id'));
                                                                    if($bilgi){
                                                                        $abonesayackayitbilgi = AboneSayacKayitBilgi::find($bilgi->id);
                                                                        $abonesayackayitbilgi->durum = 1;
                                                                        $abonesayackayitbilgi->save();
                                                                        $servistakip->abonesayackayitbilgi_id=$abonesayackayitbilgi->id;
                                                                        $servistakip->save();
                                                                        $abonesayackayit = AboneSayacKayit::find($abonesayackayitbilgi->abonesayackayit_id);
                                                                        $hatalikayitlar = AboneSayacKayitBilgi::where('abonesayackayit_id',$abonesayackayit->id)->where('durum',0)->get();
                                                                        if($hatalikayitlar->count()>0){
                                                                            $abonesayackayit->durum=2;
                                                                            $abonesayackayit->save();
                                                                        }else{
                                                                            $abonesayackayit->durum=1;
                                                                            $abonesayackayit->save();
                                                                        }
                                                                    }
                                                                } catch (Exception $e) {
                                                                    Log::error($e);
                                                                    DB::rollBack();
                                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayac Kayıdı Yapılamadı', 'text' => 'Sayac Kayıdı Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                                                }
                                                            }
                                                        } catch (Exception $e) {
                                                            Log::error($e);
                                                            DB::rollBack();
                                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayac Kayıdı Yapılamadı', 'text' => 'Sayac Kayıdı Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                                        }
                                                    }
                                                }
                                                try {
                                                    if ($biten > 0 ) {
                                                        BackendController::HatirlatmaGuncelle(2, $netsiscari_id, $servisid, $biten, $depogelen->id, $depogelen->servisstokkodu);
                                                        BackendController::DepoDurumGuncelle($depogelen->id);
                                                        BackendController::HatirlatmaEkle(3, $netsiscari_id, $servisid, $biten, $depogelen->id, $depogelen->servisstokkodu);
                                                        BackendController::BildirimEkle(2, $netsiscari_id, $servisid, $biten, $depogelen->id, $depogelen->servisstokkodu);
                                                    }
                                                } catch (Exception $e) {
                                                    DB::rollBack();
                                                    Log::error($e);
                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Giriş Bilgisi Güncellenemedi', 'text' => 'Hatırlatma Kayıdı Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                                }
                                            }else{
                                                $biten=0;
                                                $eskiler = SayacGelen::where('depogelen_id', $depogelen->id)->get();
                                                $eskisayaclar = SayacGelen::where('depogelen_id', $depogelen->id)->get();
                                                $eskisayi = $eskisayaclar->count();
                                                foreach ($eskisayaclar as $eskisayac) {
                                                    $eskisayac->flag = 0;
                                                }
                                                $yeniids = array();
                                                $allids = array();
                                                foreach ($sayac as $girilecek) {
                                                    $serino = $girilecek['serino'];
                                                    $sayacadi = $girilecek['sayacadi'];
                                                    $sayaccap = $girilecek['sayaccap'];
                                                    $uretimyeri = $girilecek['uretimyeri'];
                                                    $uretimtarihi = $girilecek['uretimyili'];
                                                    if ($serino != "") {
                                                        try {
                                                            $flag = 0;
                                                            for ($j = 0; $j < $eskiler->count(); $j++) {
                                                                $sayacgelen = $eskiler[$j];
                                                                $eskisayac = $eskisayaclar[$j];
                                                                if ($sayacgelen->serino == $serino && $eskisayac->flag == 0) {
                                                                    try {
                                                                        $flag = 1;
                                                                        $eskisayac->flag = 1;
                                                                        $sayacgelen->depogelen_id = $depogelen->id;
                                                                        $sayacgelen->netsiscari_id = $netsiscari_id;
                                                                        $sayacgelen->stokkodu = $depogelen->servisstokkodu;
                                                                        $sayacgelen->serino = $serino;
                                                                        $sayacgelen->depotarihi = $depogelen->tarih;
                                                                        $sayacgelen->sayacadi_id = $sayacadi;
                                                                        $sayacgelen->sayaccap_id = $sayaccap;
                                                                        $sayacgelen->uretimyer_id = $uretimyeri;
                                                                        $sayacgelen->kullanici_id = Auth::user()->id;
                                                                        $sayacgelen->save();
                                                                        if ($sayacgelen->servis_id == 5) {
                                                                            $sayac = Sayac::where('serino', $serino)->where('sayactur_id', 5)->where('uretimyer_id', $uretimyeri)->first();
                                                                            if ($sayac) {
                                                                                $sayac->sayacadi_id = $sayacadi;
                                                                                $sayac->sayaccap_id = $sayaccap;
                                                                                if ($uretimtarihi != "") {
                                                                                    $utarih = date('Y-m-d', mktime(0, 0, 0, 1, 1, $uretimtarihi));
                                                                                    $sayac->uretimtarihi = $utarih;
                                                                                }
                                                                                $sayac->uretimyer_id = $uretimyeri;
                                                                                $sayac->save();
                                                                            } else {
                                                                                $sayac = new Sayac;
                                                                                $sayac->serino = $serino;
                                                                                $sayac->cihazno = $serino;
                                                                                $sayac->sayactur_id = 5;
                                                                                $sayac->sayacadi_id = $sayacadi;
                                                                                $sayac->sayaccap_id = 1;
                                                                                if ($uretimtarihi != "") {
                                                                                    $utarih = date('Y-m-d', mktime(0, 0, 0, 1, 1, $uretimtarihi));
                                                                                    $sayac->uretimtarihi = $utarih;
                                                                                }
                                                                                $sayac->uretimyer_id = $uretimyeri;
                                                                                $sayac->save();
                                                                            }
                                                                        }
                                                                        $biten++;
                                                                        array_push($allids, $sayacgelen->id);
                                                                        $servistakip = ServisTakip::where('sayacgelen_id', $sayacgelen->id)->first();
                                                                        if ($servistakip) {
                                                                            $servistakip->depogelen_id = $depogelen->id;
                                                                            $servistakip->serino = $serino;
                                                                            $servistakip->sayacadi_id = $sayacadi;
                                                                            $servistakip->netsiscari_id = $netsiscari_id;
                                                                            $servistakip->uretimyer_id = $uretimyeri;
                                                                            $servistakip->save();

                                                                            if($servistakip->abonesayackayitbilgi_id != null){
                                                                                $abonesayackayitbilgi = AboneSayacKayitBilgi::find($servistakip->abonesayackayitbilgi_id);
                                                                                if(!$abonesayackayitbilgi){
                                                                                    Input::flash();
                                                                                    DB::rollBack();
                                                                                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Müşteri Kayıdı Hatalı', 'text' => ' Müşteri Kayıdı Bulunamadı!', 'type' => 'error'));
                                                                                }
                                                                                if($serino != $abonesayackayitbilgi->serino){
                                                                                    $abonesayackayit = AboneSayacKayit::find($abonesayackayitbilgi->abonesayackayit_id);
                                                                                    $eklisayackayitbilgi = AboneSayacKayitBilgi::where('abonesayackayit_id',$abonesayackayitbilgi->abonesayackayit_id)->where('serino',$serino)->where('id','<>',$servistakip->abonesayackayitbilgi_id)->first();
                                                                                    if($eklisayackayitbilgi){
                                                                                        if($eklisayackayitbilgi->durum==0){
                                                                                            $eklisayackayitbilgi->durum = 1;
                                                                                            $eklisayackayitbilgi->save();
                                                                                            $abonesayackayitbilgi->durum = 0;
                                                                                            $abonesayackayitbilgi->save();
                                                                                            $servistakip->abonesayackayitbilgi_id=$eklisayackayitbilgi->id;
                                                                                            $servistakip->save();
                                                                                        }else{
                                                                                            Input::flash();
                                                                                            DB::rollBack();
                                                                                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Müşteri Kayıdı Hatalı', 'text' => $serino . ' Nolu Sayaç için zaten kayıt mevcut!', 'type' => 'error'));
                                                                                        }
                                                                                    }else{
                                                                                        $abonesayackayitbilgi->durum=0;
                                                                                        $abonesayackayitbilgi->save();
                                                                                        $abonesayackayit->durum = 2;
                                                                                        $abonesayackayit->save();
                                                                                        $servistakip->abonesayackayitbilgi_id=null;
                                                                                        $servistakip->save();
                                                                                    }
                                                                                }
                                                                            }else{
                                                                                $bilgi = AboneSayacKayit::where('abonesayackayit.netsiscari_id',$netsiscari->id)->where('abonesayackayit.durum',2)
                                                                                    ->where('abonesayackayitbilgi.serino',$serino)
                                                                                    ->leftJoin('abonesayackayitbilgi','abonesayackayitbilgi.abonesayackayit_id','=','abonesayackayit.id')->first(array('abonesayackayitbilgi.id'));
                                                                                if($bilgi){
                                                                                    $abonesayackayitbilgi = AboneSayacKayitBilgi::find($bilgi->id);
                                                                                    $abonesayackayitbilgi->durum = 1;
                                                                                    $abonesayackayitbilgi->save();
                                                                                    $servistakip->abonesayackayitbilgi_id=$abonesayackayitbilgi->id;
                                                                                    $servistakip->save();
                                                                                    $abonesayackayit = AboneSayacKayit::find($abonesayackayitbilgi->abonesayackayit_id);
                                                                                    $hatalikayitlar = AboneSayacKayitBilgi::where('abonesayackayit_id',$abonesayackayit->id)->where('durum',0)->get();
                                                                                    if($hatalikayitlar->count()>0){
                                                                                        $abonesayackayit->durum=2;
                                                                                        $abonesayackayit->save();
                                                                                    }else{
                                                                                        $abonesayackayit->durum=1;
                                                                                        $abonesayackayit->save();
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                        break;
                                                                    } catch (Exception $e) {
                                                                        Log::error($e);
                                                                        DB::rollBack();
                                                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo kayıdı Güncellenemedi', 'text' => 'Sayac Kayıdı Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                                                    }
                                                                } else {
                                                                    continue;
                                                                }
                                                            }
                                                            if ($flag == 0) { //yeni eklemeyse
                                                                try {
                                                                    $sayacgelen = new SayacGelen;
                                                                    $sayacgelen->depogelen_id = $depogelen->id;
                                                                    $sayacgelen->netsiscari_id = $netsiscari_id;
                                                                    $sayacgelen->stokkodu = $depogelen->servisstokkodu;
                                                                    $sayacgelen->serino = $serino;
                                                                    $sayacgelen->depotarihi = $depogelen->tarih;;
                                                                    $sayacgelen->sayacadi_id = $sayacadi;
                                                                    $sayacgelen->sayaccap_id = $sayaccap;
                                                                    $sayacgelen->uretimyer_id = $uretimyeri;
                                                                    $sayacgelen->servis_id = $servisid;
                                                                    $sayacgelen->kullanici_id = $kullanici->id;
                                                                    $sayacgelen->beyanname = -2;
                                                                    $sayacgelen->save();
                                                                    array_push($yeniids, $sayacgelen->id);
                                                                    array_push($allids, $sayacgelen->id);
                                                                    if ($sayacgelen->servis_id == 5) {
                                                                        $sayac = Sayac::where('serino', $serino)->where('sayacadi_id', $sayacadi)->where('sayaccap_id', 1)->where('uretimyer_id', $uretimyeri)
                                                                            ->where('sayactur_id', 5)->first();
                                                                        if (!$sayac) // mekanik sayaç yoksa sayaç listesine eklenecek
                                                                        {
                                                                            $sayac = new Sayac;
                                                                            $sayac->serino = $serino;
                                                                            $sayac->cihazno = $serino;
                                                                            $sayac->sayactur_id = 5;
                                                                            $sayac->sayacadi_id = $sayacadi;
                                                                            $sayac->sayaccap_id = 1;
                                                                            if ($uretimtarihi != "") {
                                                                                $utarih = date('Y-m-d', mktime(0, 0, 0, 1, 1, $uretimtarihi));
                                                                                $sayac->uretimtarihi = $utarih;
                                                                            }
                                                                            $sayac->uretimyer_id = $uretimyeri;
                                                                            $sayac->save();
                                                                        }
                                                                    }
                                                                    $biten++;
                                                                    $servistakip = new ServisTakip;
                                                                    $servistakip->serino = $sayacgelen->serino;
                                                                    $servistakip->sayacadi_id = $sayacgelen->sayacadi_id;
                                                                    $servistakip->depogelen_id = $sayacgelen->depogelen_id;
                                                                    $servistakip->netsiscari_id = $sayacgelen->netsiscari_id;
                                                                    $servistakip->sayacgelen_id = $sayacgelen->id;
                                                                    $servistakip->servis_id = $sayacgelen->servis_id;
                                                                    $servistakip->uretimyer_id = $sayacgelen->uretimyer_id;
                                                                    $servistakip->durum = 1;
                                                                    $servistakip->depotarih = $sayacgelen->depotarihi;
                                                                    $servistakip->sayacgiristarihi = $sayacgelen->eklenmetarihi;
                                                                    $servistakip->kullanici_id = $kullanici->id;
                                                                    $servistakip->sonislemtarihi = $sayacgelen->eklenmetarihi;
                                                                    $servistakip->save();

                                                                    $bilgi = AboneSayacKayit::where('abonesayackayit.netsiscari_id',$netsiscari->id)->where('abonesayackayit.durum',2)
                                                                        ->where('abonesayackayitbilgi.serino',$serino)
                                                                        ->leftJoin('abonesayackayitbilgi','abonesayackayitbilgi.abonesayackayit_id','=','abonesayackayit.id')->first(array('abonesayackayitbilgi.id'));
                                                                    if($bilgi){
                                                                        $abonesayackayitbilgi = AboneSayacKayitBilgi::find($bilgi->id);
                                                                        $abonesayackayitbilgi->durum = 1;
                                                                        $abonesayackayitbilgi->save();
                                                                        $servistakip->abonesayackayitbilgi_id=$abonesayackayitbilgi->id;
                                                                        $servistakip->save();
                                                                        $abonesayackayit = AboneSayacKayit::find($abonesayackayitbilgi->abonesayackayit_id);
                                                                        $hatalikayitlar = AboneSayacKayitBilgi::where('abonesayackayit_id',$abonesayackayit->id)->where('durum',0)->get();
                                                                        if($hatalikayitlar->count()>0){
                                                                            $abonesayackayit->durum=2;
                                                                            $abonesayackayit->save();
                                                                        }else{
                                                                            $abonesayackayit->durum=1;
                                                                            $abonesayackayit->save();
                                                                        }
                                                                    }
                                                                } catch (Exception $e) {
                                                                    Log::error($e);
                                                                    DB::rollBack();
                                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayac Kayıdı Yapılamadı', 'text' => 'Sayac Kayıdı Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                                                }
                                                            }
                                                        } catch (Exception $e) {
                                                            Log::error($e);
                                                            DB::rollBack();
                                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayac Kayıdı Yapılamadı', 'text' => 'Sayac Kayıdı Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                                        }
                                                    }
                                                }
                                                for ($j = 0; $j < $eskisayaclar->count(); $j++) {
                                                    $sayacgelen = $eskisayaclar[$j];
                                                    if ($sayacgelen->flag == 0) { // silinecek varsa
                                                        try {
                                                            $sayacgelen = SayacGelen::find($sayacgelen->id);
                                                            $servistakip = ServisTakip::where('sayacgelen_id', $sayacgelen->id)->first();
                                                            if ($sayacgelen->servis_id == 5) {
                                                                $sayac = Sayac::where('serino', $sayacgelen->serino)->where('uretimyer_id', $sayacgelen->uretimyeri_id)
                                                                    ->where('sayactur_id', 5)->first();
                                                                if ($sayac) { // mekanik sayaç varsa ve başka kayıdı yoksa silinecek
                                                                    $eskisayacgelen = SayacGelen::where('serino', $sayacgelen->serino)->where('sayactur_id', 5)->where('id', '<>', $sayacgelen->id)->first();
                                                                    if (!$eskisayacgelen)
                                                                        $sayac->delete();
                                                                }
                                                            }
                                                            if($servistakip->abonesayackayitbilgi_id != null){
                                                                $abonesayackayitbilgi = AboneSayacKayitBilgi::find($servistakip->abonesayackayitbilgi_id);
                                                                if(!$abonesayackayitbilgi){
                                                                    Input::flash();
                                                                    DB::rollBack();
                                                                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Müşteri Kayıdı Hatalı', 'text' => ' Müşteri Kayıdı Bulunamadı!', 'type' => 'error'));
                                                                }
                                                                $abonesayackayit = AboneSayacKayit::find($abonesayackayitbilgi->abonesayackayit_id);
                                                                $abonesayackayitbilgi->durum=0;
                                                                $abonesayackayitbilgi->save();
                                                                $abonesayackayit->durum = 2;
                                                                $abonesayackayit->save();
                                                                $servistakip->abonesayackayitbilgi_id=null;
                                                                $servistakip->save();
                                                            }
                                                            if($biten != $eskisayi){
                                                                BackendController::HatirlatmaSil(3, $netsiscari_id, $servisid, 1, $depogelen->id, $depogelen->servisstokkodu);
                                                                BackendController::BildirimGeriAl(2, $netsiscari_id, $servisid, 1, $depogelen->id, $depogelen->servisstokkodu);
                                                            }
                                                            $servistakip->delete();
                                                            $sayacgelen->delete();
                                                        } catch (Exception $e) {
                                                            Log::error($e);
                                                            DB::rollBack();
                                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Eski Sayaç Kayıdı Silinemedi', 'text' => 'Eski Sayaç Kayıdı Silinirken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                                        }
                                                    }
                                                }
                                                try {
                                                    if ($biten > 0 && $biten != $eskisayi) {
                                                        $sayi = $biten - $eskisayi; //silinme durumu yukarda çözüldü ekleme varsa eklenecek
                                                        if ($sayi > 0) {
                                                            BackendController::HatirlatmaGuncelle(2, $netsiscari_id, $servisid, $sayi, $depogelen->id, $depogelen->servisstokkodu);
                                                            BackendController::DepoDurumGuncelle($depogelen->id);
                                                            BackendController::HatirlatmaEkle(3, $netsiscari_id, $servisid, $sayi, $depogelen->id, $depogelen->servisstokkodu);
                                                            BackendController::BildirimEkle(2, $netsiscari_id, $servisid, $sayi, $depogelen->id, $depogelen->servisstokkodu);
                                                        }
                                                    }
                                                } catch (Exception $e) {
                                                    DB::rollBack();
                                                    Log::error($e);
                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Giriş Bilgisi Güncellenemedi', 'text' => 'Hatırlatma Kayıdı Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    DB::rollBack();
                                    Input::flash();
                                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Depo Giriş Bilgisi Güncellenemedi', 'text' => 'Depo Gelen Bilgisi Kaydedilmemiş', 'type' => 'error'));
                                }
                            }
                        }
                    } catch (Exception $e) {
                        DB::rollBack();
                        Log::error($e);
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Giriş Bilgisi Güncellenemedi', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
                    }
                    BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-thumbs-o-up', $depogelen->id . ' Nolu Depo Girişi Güncellendi.', 'Güncelleme Yapan:' . $kullanici->adi_soyadi . ',Depo Giriş Numarası:' . $depogelen->id);
                    DB::commit();
                    return Redirect::to('depo/depogelen')->with(array('mesaj' => 'true', 'title' => 'Depo Giriş Bilgisi Güncellendi', 'text' => 'Depo Giriş Bilgisi Başarıyla Güncellendi', 'type' => 'success'));
                }else{
                    Input::flash();
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Ambar Girişi Kaydedilemedi', 'text' => 'Girilen Sayaçların Seri Numaraları yazılmamış', 'type' => 'error'));
                }
            }
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            Input::flash();
            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Depo Giriş Bilgisi Güncellenemedi', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getDepogelensil($id){
        try {
            $depogelen = DepoGelen::find($id);
            if ($depogelen) {
                DB::beginTransaction();
                $bilgi =clone $depogelen;
                if (!BackendController::getDepoKayitDurum($id)) {
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Giriş Kayıdı Silinemedi', 'text' => 'Arıza Kayıdı Yapılmış Sayaç Var!', 'type' => 'error'));
                }
                $dbname = $depogelen->db_name;
                $inckey = $depogelen->inckeyno;
                $fisno = $depogelen->fisno;
                $kullanici = Kullanici::find(Auth::user()->id);
                $durum = 0;
                $diger = DepoGelen::where('fisno', $fisno)->where('db_name', $dbname)->get();
                if ($diger->count() > 1)
                    $durum = 1;
                if($depogelen->servis_id==6){
                    $subeyetkili = SubeYetkili::where('kullanici_id', Auth::user()->id)->where('aktif',1)->first();
                    if (!$subeyetkili){
                        Input::flash();
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kullanıcı Yetkili Değil', 'text' => 'Kullanıcı Şube için Yetkili Olarak Eklenmemiş!', 'type' => 'error'));
                    }
                    $depogiris = BackendController::SubeDepoGirisSil($subeyetkili->subekodu,$inckey,$fisno,$durum);
                }else{
                    $depogiris = BackendController::NetsisDepoGirisSil($inckey,$fisno,$durum);
                }
                if ($depogiris['durum'] == '0') {
                    DB::rollBack();
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Ambar Girişi Silinemedi', 'text' => $depogiris['text'], 'type' => 'error'));
                }
                $sayacgelenler = SayacGelen::where('depogelen_id', $id)->get();
                $servistakip = ServisTakip::where('depogelen_id', $id)->get();
                $hatirlatmalar = Hatirlatma::where('depogelen_id', $id)->get();
                try {
                    foreach ($sayacgelenler as $sayacgelen) {
                        $sayacgelen->depogelen_id = NULL;
                        $sayacgelen->save();
                        if($sayacgelen->servis_id==5) {
                            $sayac = Sayac::where('serino', $sayacgelen->serino)->where('uretimyer_id', $sayacgelen->uretimyeri_id)
                                ->where('sayactur_id', 5)->first();
                            if ($sayac){ // mekanik sayaç varsa ve başka kayıdı yoksa silinecek
                                $eskisayacgelen = SayacGelen::where('serino',$sayacgelen->serino)->where('sayactur_id',5)->where('id','<>',$sayacgelen->id)->first();
                                if(!$eskisayacgelen)
                                    $sayac->delete();
                            }
                        }else if($sayacgelen->servis_id==6){
                            $depolararasi = Depolararasi::where('netsiscari_id', $sayacgelen->netsiscari_id)->where('servis_id', 6)->where('tipi', 0)
                                ->where('depodurum', 0)->get();
                            foreach ($depolararasi as $depoislem) {
                                $secilenlist = explode(',', $depoislem->secilenler);
                                if (in_array($sayacgelen->id, $secilenlist)) { //sayaç bu listedeyse
                                    $secilenler = "";
                                    foreach ($secilenlist as $secilen) {
                                        if ($secilen != $sayacgelen->id)
                                            $secilenler = ($secilenler == "" ? "" : ",") . $secilen;
                                    }
                                    if ($secilenler != "") {
                                        $depoislem->secilenler = $secilenler;
                                        $depoislem->save();
                                    } else {
                                        $depoislem->delete();
                                    }
                                    break;
                                }
                            }
                            $abonesayac = AboneSayac::where('serino',$sayacgelen->serino)->first();
                            if($abonesayac){
                                $abonetahsis = AboneTahsis::where('abonesayac_id',$abonesayac->id)->first();
                                if($abonetahsis){
                                    $serviskayit = ServisKayit::where('depogelen_id',$depogelen->id)->where('abonetahsis_id',$abonetahsis->id)->first();
                                    if($serviskayit){
                                        $serviskayit->depogelen_id = null;
                                        $serviskayit->sokulmedurumu = 0;
                                        $serviskayit->servissayaci = $sayacgelen->takilmatarihi==NULL ? 0 : 1;
                                        $serviskayit->takilmatarihi = $sayacgelen->takilmatarihi;
                                        $serviskayit->ilkendeks = $sayacgelen->endeks;
                                        $serviskayit->save();
                                    }
                                }
                            }
                        }
                    }
                    foreach ($servistakip as $takip) {
                        if($takip->abonesayackayitbilgi_id != null){
                            $abonesayackayitbilgi = AboneSayacKayitBilgi::find($takip->abonesayackayitbilgi_id);
                            if(!$abonesayackayitbilgi){
                                Input::flash();
                                DB::rollBack();
                                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Müşteri Kayıdı Hatalı', 'text' => ' Müşteri Kayıdı Bulunamadı!', 'type' => 'error'));
                            }
                            $abonesayackayit = AboneSayacKayit::find($abonesayackayitbilgi->abonesayackayit_id);
                            $abonesayackayitbilgi->durum=0;
                            $abonesayackayitbilgi->save();
                            $abonesayackayit->durum = 2;
                            $abonesayackayit->save();
                            $takip->abonesayackayitbilgi_id=null;
                            $takip->save();
                        }
                        $takip->depogelen_id = NULL;
                        $takip->save();
                    }
                    foreach ($hatirlatmalar as $hatirlatma) {
                        $hatirlatma->depogelen_id = NULL;
                        $hatirlatma->save();
                    }
                    $depogelen->delete();
                    foreach ($servistakip as $takip) {
                        $takip->delete();
                    }
                    foreach ($sayacgelenler as $sayacgelen) {
                        $sayacgelen->delete();
                    }
                    foreach ($hatirlatmalar as $hatirlatma) {
                        $hatirlatma->delete();
                    }
                    BackendController::IslemEkle(3, Auth::user()->id, 'label-danger', 'fa-thumbs-o-up', $bilgi->id . ' Nolu Depo Girişi Silindi.', 'Güncelleme Yapan:' . $kullanici->adi_soyadi . ',Depo Giriş Numarası:' . $bilgi->id);
                    DB::commit();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Giriş Kayıdı Silindi', 'text' => 'Depo Giriş Kayıdı Başarıyla Silindi.', 'type' => 'success'));
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Giriş Kayıdı Silinemedi', 'text' => 'Depo Giriş Kayıdı Silinirken Sorun Oluştu.', 'type' => 'error'));
                }
            } else {
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Giriş Kayıdı Silinemedi', 'text' => 'Depo Giriş Kayıdı Bulunamadı.', 'type' => 'error'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Giriş Kayıdı Silinemedi', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getServiskod()
    {
        try {
            $sayacadiid=Input::get('sayacadiid');
            if (Input::has('sayaccapid')) {
                $sayaccapid=Input::get('sayaccapid');
                $sayacparca = SayacParca::where('sayacadi_id', $sayacadiid)->where('sayaccap_id', $sayaccapid)->first();
            } else {
                $sayacparca = SayacParca::where('sayacadi_id', $sayacadiid)->first();
            }
            if ($sayacparca) {
                $serviskod = $sayacparca->servisstokkod_id;
                return Response::json(array('durum' => true, 'serviskod' => $serviskod));
            } else {
                return Response::json(array('durum' => false, 'title' => 'Servis Kodu Bulunamadı', 'text' => 'Seçilen Sayaç Adı Servis Kodu ile uyumlu değil.', 'type' => 'error'));
            }
        } catch (Exception $e) {
            return Response::json(array('durum' => false, 'title' => 'Servis Kodu Hatası', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getCariyer()
    {
        try {
            $netsiscariid=Input::get('netsiscariid');
            $cariyerler = CariYer::where('netsiscari_id', $netsiscariid)->where('durum',1)->get(array('uretimyer_id'))->toArray();
            $uretimyer = Uretimyer::whereIn('id', $cariyerler)->get();
            if ($uretimyer->count() > 0) {
                return Response::json(array('durum' => true, 'uretimyer' => $uretimyer));
            } else {
                return Response::json(array('durum' => false, 'title' => 'Seçilen Cari ile Bir Üretim Yeri Eşleştirilmemiş!', 'text' => 'Önce Cari Bilgisi ile bir Üretim Yeri Eşleştirin!', 'type' => 'error'));
            }
        } catch (Exception $e) {
            return Response::json(array('durum' => false, 'title' => 'Cari Bilgi - Üretim Yeri Bilgi Hatası', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function postCikisyap($id){
        try {
            DB::beginTransaction();
            $belge1 = 'HURDA SAYAÇTIR. FATURA EDİLMEYECEKTİR.';
            $secilenler = Input::get('cikissecilenler');
            $secilenlist = explode(',', $secilenler);
            $sayacsayisi = count($secilenlist);
            $depoteslim = DepoTeslim::find($id);
            if(SayacGelen::whereIn('id',$secilenlist)->where('depoteslim',1)->get()->count()>0){
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Çıkışı Tamamlanamadı!', 'text' => 'Seçilen Depo Teslimatındaki Sayaçların Çıkışı Zaten Yapılmış!', 'type' => 'warning'));
            }
            $servisid = $depoteslim->servis_id;
            $netsiscariid = $depoteslim->netsiscari_id;
            $arizafiyatlar = ArizaFiyat::whereIn('sayacgelen_id', $secilenlist)->get();
            $depoteslim->depodurum = 1;
            $depoteslim->kullanici_id = Auth::user()->id;
            $depoteslim->teslimtarihi = date('Y-m-d H:i:s');
            $depoteslim->belge1 = $belge1;
            $depoteslim->db_name = Config::get('database.connections.sqlsrv2.database');
            $depoteslim->save();
            foreach ($arizafiyatlar as $arizafiyat) {
                $sayacgelen = SayacGelen::find($arizafiyat->sayacgelen_id);
                $sayacgelen->depoteslim = 1;
                $sayacgelen->beyanname = -2;
                $sayacgelen->save();
                $servistakip = ServisTakip::where('arizafiyat_id', $arizafiyat->id)->first();
                $servistakip->depoteslim_id = $depoteslim->id;
                $servistakip->durum = 9;
                $servistakip->depoteslimtarihi = $depoteslim->teslimtarihi;
                $servistakip->kullanici_id = Auth::user()->id;
                $servistakip->sonislemtarihi = $depoteslim->teslimtarihi;
                $servistakip->save();
                $sayac = Sayac::find($arizafiyat->sayac_id);
                $sayac->songelistarihi = $sayacgelen->depotarihi;
                $sayac->save();
            }
            BackendController::HatirlatmaGuncelle(9, $netsiscariid, $servisid, $sayacsayisi);
            BackendController::BildirimEkle(9, $netsiscariid, $servisid, $sayacsayisi);
            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-truck', $depoteslim->id . ' Nolu Depo Teslimatı Hurda Olarak Yapıldı.', 'Kayıt Yapan:' . Auth::user()->adi_soyadi . ',Depo Teslimat Numarası:' . $depoteslim->id);
            DB::commit();
            return Redirect::to('depo/depoteslim')->with(array('mesaj' => 'true', 'title' => 'Depo Çıkışı Başarıyla Yapıldı', 'text' => 'Sayaçların Hurda Olarak Depo Çıkışı Yapıldı. Fatura kesilmedi.', 'type' => 'success'));

        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Çıkışı Yapılamadı.', 'text' => 'Depo Çıkışı Yapılırken Hata Oluştu.', 'type' => 'error'));
        }
    }

    public function getDepolararasi() {
        $netsisdepolar=NetsisDepolar::whereIn('kodu',array(6,11,12))->get();
        foreach($netsisdepolar as $netsisdepo){
            $netsisdepo->netsiscari=NetsisCari::find($netsisdepo->netsiscari_id);
        }
        return View::make('depo.depolararasi',array('netsisdepolar'=>$netsisdepolar))->with(array('title'=>'Depolararası Transfer Ekranı'));
    }

    public function postDepolararasilist() {
        $netsiscari_id=Input::get('netsiscari_id');
        if($netsiscari_id!=""){
            $netsiscarilist=explode(',',$netsiscari_id);
            $query = Depolararasi::whereIn('depolararasi.netsiscari_id',$netsiscarilist)
                ->select(array("depolararasi.id","netsiscari.cariadi","depolararasi.sayacsayisi","depolararasi.gtipi","depolararasi.gdepodurum",
                    "netsisdepolar.adi","kullanici.adi_soyadi","depolararasi.teslimtarihi","depolararasi.gteslimtarihi","netsiscari.ncariadi","depolararasi.ntipi",
                    "depolararasi.ndepodurum"))
                ->leftjoin("netsiscari", "depolararasi.netsiscari_id", "=", "netsiscari.id")
                ->leftJoin("netsisdepolar", "depolararasi.aktarilandepo", "=", "netsisdepolar.kodu")
                ->leftJoin("kullanici", "depolararasi.kullanici_id", "=", "kullanici.id");
        }else{
            $query = Depolararasi::select(array("depolararasi.id","netsiscari.cariadi","depolararasi.sayacsayisi","depolararasi.gtipi","depolararasi.gdepodurum",
                "netsisdepolar.adi","kullanici.adi_soyadi","depolararasi.teslimtarihi","depolararasi.gteslimtarihi","netsiscari.ncariadi","depolararasi.ntipi",
                "depolararasi.ndepodurum"))
                ->leftjoin("netsiscari", "depolararasi.netsiscari_id", "=", "netsiscari.id")
                ->leftJoin("netsisdepolar", "depolararasi.aktarilandepo", "=", "netsisdepolar.kodu")
                ->leftJoin("kullanici", "depolararasi.kullanici_id", "=", "kullanici.id");
        }
        return Datatables::of($query)
            ->editColumn('teslimtarihi', function ($model) {
                if ($model->teslimtarihi != null) {
                    $date = new DateTime($model->teslimtarihi);
                    return $date->format('d-m-Y');
                }else{
                    return '';
                }})
            ->addColumn('islemler',function ($model) use($netsiscari_id) {
                if($model->gdurum=='Bekliyor')
                    return "<a class='btn btn-sm btn-warning detay' href='#detay-goster' data-toggle='modal' data-id='{$model->id}'> Detay </a>
                    <a href='#portlet-delete' data-toggle='modal' data-id='{$model->id}' class='btn btn-sm btn-danger delete' data-original-title='' title=''>Sil</a>";
                else
                    return "<a class='btn btn-sm btn-warning detay' href='#detay-goster' data-toggle='modal' data-id='{$model->id}'> Detay </a>";
            })
            ->make(true);
    }

    public function getDepolararasibilgi() {
        try {
            $id=Input::get('id');
            $depolararasi = Depolararasi::find($id);
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

    public function getAktarmabilgi() {
        try {
            $id=Input::get('id');
            $depolararasi = Depolararasi::find($id);
            $depolararasi->netsiscari = Netsiscari::find($depolararasi->netsiscari_id);
            $depolararasi->yetkili = ServisYetkili::where('kullanici_id', Auth::user()->id)->first();
            if (!$depolararasi->yetkili)
                return Response::json(array('durum' => false,'title'=>'Aktarma Bilgisi Hatası.','text'=>'Kullanici Servis birimine kayıtlı değil','type'=>'warning'));
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
                if ($sayacgelen->eklenmetarihi)
                    $sayacgelen->tarih = date('d-m-Y', strtotime($sayacgelen->eklenmetarihi));
                else
                    $sayacgelen->tarih = "";
            }
            $teslimadres = Depolararasi::selectRaw('count(*) AS sayi, teslimadres')->where('netsiscari_id',$depolararasi->netsiscari_id)->where('depodurum',1)->where('teslimadres','<>','')->groupBy('teslimadres')->orderBy('sayi', 'DESC')->get();
            return Response::json(array('durum' => true, 'depolararasi' => $depolararasi,'teslimadres'=>$teslimadres ));
        } catch (Exception $e) {
            return Response::json(array('durum' => false, 'title'=>'Aktarma Bilgisi Hatası.','text'=>str_replace("'","\'",$e->getMessage()),'type'=>'error' ));
        }
    }

    public function postDepolararasi()
    { /*try {
        if (Input::has('irsaliye')) {
            $depolararasiid = Input::get('irsaliye');
            $depolararasi = Depolararasi::find($depolararasiid);
            if ($depolararasi) {
                $depolararasi->kullanici = Kullanici::find($depolararasi->kullanici_id);
                $depolararasi->netsiscari = NetsisCari::find($depolararasi->netsiscari_id);
                $netsisdepo = NetsisDepolar::where('kodu', $depolararasi->aktarilandepo)->first();
                $depolararasi->aktarilan = NetsisCari::find($netsisdepo->netsiscari_id);
                if ($depolararasi->teslimtarihi)
                    $depolararasi->teslimtarihi = date('d-m-Y', strtotime($depolararasi->teslimtarihi));
                else
                    $depolararasi->teslimtarihi = "";
                $secilenler = explode(',', $depolararasi->secilenler);
                $sayacgelenler = SayacGelen::whereIn('id', $secilenler)->get();
                foreach ($sayacgelenler as $sayacgelen) {
                    $sayacgelen->uretimyer = UretimYer::find($sayacgelen->uretimyer_id);
                    $sayacgelen->adet = 1;
                    $sayacgelen->sayacadi = SayacAdi::find($sayacgelen->sayacadi_id);
                    $sayacgelen->sayaccap = SayacCap::find($sayacgelen->sayaccap_id);
                    $stokadi = ServisStokKod::where('stokkodu', $sayacgelen->stokkodu)->first();
                    $sayacgelen->stokadi = $stokadi->stokadi;
                }
                $sayacgelenlist = $sayacgelenler->toArray();
                $depolararasi->gruplar = BackendController::SubeSayacGrupla($sayacgelenlist);
                $depolararasi->sayacgelenler = $sayacgelenler;
                $pdf = PDF::loadView('pdf.depolararasi', array('depolararasi' => $depolararasi));
                return $pdf->download('Depolararasi-' . $depolararasi->netsiscari->cariadi . '.pdf');
            } else {
                return Redirect::back()->with(array('mesaj' => 'true', 'text' => 'Rapor alınacak teslimat seçilmedi', 'type' => 'warning', 'title' => 'Rapor Hatası'));
            }
        } else { //sayaç listesi
            $depolararasiid = Input::get('irsaliyeek');
            $depolararasi = Depolararasi::find($depolararasiid);
            if ($depolararasi) {
                $depolararasi->kullanici = Kullanici::find($depolararasi->kullanici_id);
                $depolararasi->netsiscari = NetsisCari::find($depolararasi->netsiscari_id);
                $netsisdepo = NetsisDepolar::where('kodu', $depolararasi->aktarilandepo)->first();
                $depolararasi->aktarilan = NetsisCari::find($netsisdepo->netsiscari_id);
                if ($depolararasi->teslimtarihi)
                    $depolararasi->teslimtarihi = date('d-m-Y', strtotime($depolararasi->teslimtarihi));
                else
                    $depolararasi->teslimtarihi = "";
                $secilenler = explode(',', $depolararasi->secilenler);
                $sayacgelenler = SayacGelen::whereIn('id', $secilenler)->get();
                foreach ($sayacgelenler as $sayacgelen) {
                    $sayacgelen->uretimyer = UretimYer::find($sayacgelen->uretimyer_id);
                    $sayacgelen->adet = 1;
                    $sayacgelen->sayacadi = SayacAdi::find($sayacgelen->sayacadi_id);
                    $sayacgelen->sayaccap = SayacCap::find($sayacgelen->sayaccap_id);
                    $stokadi = ServisStokKod::where('stokkodu', $sayacgelen->stokkodu)->first();
                    $sayacgelen->stokadi = $stokadi->stokadi;
                    $sayacgelen->durum = 'Depolararası Sevk';
                }
                $depolararasi->sayacgelenler = $sayacgelenler;
                $pdf = PDF::loadView('pdf.sayaclistesi', array('depolararasi' => $depolararasi));
                return $pdf->download('Sayaç Listesi-' . $depolararasi->netsiscari->cariadi . '.pdf');
            } else {
                return Redirect::back()->with(array('mesaj' => 'true', 'text' => 'Sayaç Listesi alınacak teslimat seçilmedi', 'type' => 'warning', 'title' => 'Rapor Hatası'));
            }
        }
    } catch (Exception $e) {
        return Redirect::back()->with(array('mesaj' => 'true', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error', 'title' => 'Rapor Hatası'));
    }*/
        try {
            if (Input::has('irsaliye')) {
                $depolararasiid=Input::get('irsaliye');
                $depolararasi = Depolararasi::find($depolararasiid);
                if ($depolararasi) {
                    $depolararasi->netsiscari=NetsisCari::find($depolararasi->netsiscari_id);
                    $raporadi="Depolararasi-".Str::slug($depolararasi->netsiscari->cariadi);
                    $export="pdf";
                    $kriterler=array();
                    $kriterler['id']=$depolararasiid;

                    JasperPHP::process( public_path('reports/depolararasiirsaliye/depolararasiirsaliye.jasper'), public_path('reports/outputs/depolararasiirsaliye/'.$raporadi),
                        array($export), $kriterler,
                        Config::get('database.connections.report') )->execute();
                    if($export=='pdf'){
                        header("Content-type:application/pdf");
                        header("Content-Disposition:inline;filename=".$raporadi.".".$export."");
                    }else if($export=='xls'){
                        header("Content-Type:   application/vnd.ms-excel");
                        header("Content-Disposition: inline; filename=".$raporadi.".".$export."");
                    }else{
                        //header('Content-Type: application/octet-stream');
                        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
                        header("Content-Disposition: inline; filename=".$raporadi.".".$export."");
                    }
                    readfile("reports/outputs/depolararasiirsaliye/".$raporadi.".".$export."");
                    File::delete("reports/outputs/depolararasiirsaliye/".$raporadi.".".$export."");
                    return Redirect::back()->with(array('mesaj' => 'false'));
                } else {
                    return Redirect::back()->with(array('mesaj' => 'true', 'text' => 'Rapor alınacak teslimat seçilmedi', 'type' => 'warning', 'title' => 'Rapor Hatası'));
                }
            } else { //sayaç listesi
                $depolararasiid=Input::get('irsaliyeek');
                $depolararasi = Depolararasi::find($depolararasiid);
                if ($depolararasi) {
                    $depolararasi->netsiscari=NetsisCari::find($depolararasi->netsiscari_id);
                    $raporadi="SayacListesi-".Str::slug($depolararasi->netsiscari->cariadi);
                    $export="pdf";
                    $kriterler=array();
                    $kriterler['id']=$depolararasiid;

                    JasperPHP::process( public_path('reports/depolararasisayaclistesi/depolararasisayaclistesi.jasper'), public_path('reports/outputs/depolararasisayaclistesi/'.$raporadi),
                        array($export), $kriterler,
                        Config::get('database.connections.report') )->execute();
                    if($export=='pdf'){
                        header("Content-type:application/pdf");
                        header("Content-Disposition:inline;filename=".$raporadi.".".$export."");
                    }else if($export=='xls'){
                        header("Content-Type:   application/vnd.ms-excel");
                        header("Content-Disposition: inline; filename=".$raporadi.".".$export."");
                    }else{
                        //header('Content-Type: application/octet-stream');
                        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
                        header("Content-Disposition: inline; filename=".$raporadi.".".$export."");
                    }
                    readfile("reports/outputs/depolararasisayaclistesi/".$raporadi.".".$export."");
                    File::delete("reports/outputs/depolararasisayaclistesi/".$raporadi.".".$export."");
                    return Redirect::back()->with(array('mesaj' => 'false'));
                } else {
                    return Redirect::back()->with(array('mesaj' => 'true', 'text' =>'Sayaç Listesi alınacak teslimat seçilmedi', 'type' => 'warning','title' => 'Rapor Hatası'));
                }
            }
        } catch (Exception $e) {
            return Redirect::back()->with(array('mesaj' => 'true', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error', 'title' => 'Rapor Hatası'));
        }

    }

    public function postAktar($id){ //şubeden servise sayaç aktarımı
        try{
            DB::beginTransaction();
            $depolararasi = Depolararasi::find($id);
            if(Input::has('faturavar')) //fatura basılacak
                $fatura=1;
            else // fatura basılmayacak
                $fatura=0;

            $adres=Input::get('teslimadres');

            $secilenler = Input::get('teslimsecilenler');
            $secilenlist=explode(',',$secilenler);
            $tumu=explode(',',Input::get('teslimtumu'));
            $aktarilandepo = Input::get('teslimaktarilan');
            $aktarilandepocari=null;
            if($aktarilandepo){
                $netsisdepo = NetsisDepolar::where('kodu', $aktarilandepo)->first();
                $aktarilandepocari = NetsisCari::find($netsisdepo->netsiscari_id);
            }
            $faturano = NULL;
            if (Input::has('teslimfaturano')) {
                $faturano = Input::get('teslimfaturano');
            }
            if($depolararasi->depodurum==1){
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depolararası Aktarma Tamamlanamadı!', 'text' => 'Seçilen Teslimattaki Sayaçların Aktarımı Zaten Yapılmış!', 'type' => 'warning'));
            }
            $netsiscariid = $depolararasi->netsiscari_id;
            $sayacgelenler = SayacGelen::whereIn('id', $secilenlist)->get();
            $sayacgelenlist = $sayacgelenler->toArray();
            $stokkodu = array();
            foreach ($sayacgelenlist as $key => $row) {
                $stokkodu[$key] = $row['stokkodu'];
            }
            array_multisort($stokkodu, SORT_ASC, $sayacgelenlist);
            $yetkili = ServisYetkili::where('kullanici_id', Auth::user()->id)->first();
            $netsiscari= NetsisCari::find($depolararasi->netsiscari_id);
            if($netsiscari->caridurum!="A")
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Netsis Cari Uyarısı', 'text' => 'Cari Bilgisi Netsis Üzerinde Kilitli.', 'type' => 'warning'));
            if(count($secilenlist)==count($tumu)) //depo teslimdeki tum sayaçlar seçilmiş
            {
                try{
                    $depolararasi->depodurum=1;
                    $depolararasi->kullanici_id= Auth::user()->id;
                    $depolararasi->teslimtarihi=date('Y-m-d H:i:s');
                    $depolararasi->faturano = $faturano;
                    $depolararasi->carikod=$netsiscari->carikod;
                    $depolararasi->ozelkod='0';
                    $depolararasi->plasiyerkod = $yetkili->plasiyerkod;
                    $depolararasi->aktarilandepo = $aktarilandepo;
                    $depolararasi->netsiskullanici = $yetkili->netsiskullanici;
                    $depolararasi->faturaadres = $aktarilandepo ? $aktarilandepocari->adres . ' ' . $aktarilandepocari->ilce . ' ' . $aktarilandepocari->il : "";
                    $depolararasi->teslimadres = $adres;
                    $depolararasi->save();
                    foreach ($sayacgelenler as $sayacgelen){
                        $servistakip=ServisTakip::where('sayacgelen_id',$sayacgelen->id)->first();
                        $servistakip->depolararasi_id=$depolararasi->id;
                        $servistakip->depolararasitarihi=$depolararasi->teslimtarihi;
                        if($depolararasi->tipi==1)
                            $servistakip->durum=11;
                        else
                            $servistakip->durum=12;
                        $servistakip->kullanici_id = Auth::user()->id;
                        $servistakip->save();
                        $sayacgelen->teslimdurum = $sayacgelen->teslimdurum==2 ? 2 : 1; //şube geri gonderilen sayac 2 teslim edilen 1 //0 bekliyor 1 teslimat 2 geri gönderim 3 hurda 4 depolararasi 5 periyodik bakım
                        $sayacgelen->save();
                    }
                }catch (Exception $e){
                    Log::error($e);
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Teslimatı yapılamadı', 'text' => 'Depo Teslimatı yapılırken hata oluştu', 'type' => 'error'));
                }
                if($fatura==0) //fatura basılmayacaksa
                {
                    $durum=0;
                    $faturakalemler = array();
                    $teslimdurum=array();
                }else {
                    $eklenenler = BackendController::NetsisSubeDepolararasi($id);
                    $faturakalemler = $eklenenler['kalemler'];
                    $teslimdurum=$eklenenler['teslimdurum'];
                    $durum = $eklenenler['durum'];
                }
                if($durum==0 || $durum==1)
                {
                    try{
                        foreach ($sayacgelenler as $sayacgelen) {

                            $sayacgelen->depolararasi =1;
                            $sayacgelen->save();
                        }
                    } catch (Exception $e) {
                        Log::error($e);
                        DB::rollBack();
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Teslimatı yapılamadı', 'text' => 'Sayaç Gelen Bilgisi Güncellenemedi.', 'type' => 'error'));
                    }
                    try {
                        $i=0;
                        foreach ($faturakalemler as $inckey) {
                            $dbname = 'MANAS' . date('Y');
                            $depogelen = DepoGelen::where('db_name', $dbname)->where('inckeyno', $inckey)->first();
                            $now = time();
                            while ($now + 30 > time()) {
                                $depogelen = DepoGelen::where('db_name', $dbname)->where('inckeyno', $inckey)->first();
                                if ($depogelen)
                                    break;
                            }
                            if ($depogelen) {
                                $servisid=$depogelen->servis_id;
                                if($teslimdurum[$i]==2){ // //şubeden geri gönderilen sayaçlar için işlem yapılmayacak eski parçaları takılıp gönderilecek ya da hurda ayrılacak

                                    $hatirlatma = Hatirlatma::where('hatirlatmatip_id', 2)->where('servisstokkodu', $depogelen->servisstokkodu)
                                        ->where('depogelen_id', $depogelen->id)->where('tip', 2)->first();
                                    $hatirlatma->durum = 1;
                                    $hatirlatma->kalan = 0;
                                    $hatirlatma->save();
                                    $depogelen = DepoGelen::where('db_name', $dbname)->where('inckeyno', $inckey)->first();
                                    $depogelen->durum=2;
                                    $depogelen->save();
                                    foreach ($sayacgelenler as $sayacgelen){
                                        $servistakip=ServisTakip::where('sayacgelen_id',$sayacgelen->id)->first();
                                        $ucretlendirilen=Ucretlendirilen::find($servistakip->ucretlendirilen_id);
                                        $ucretlendirilen->servisdurum=$sayacgelen->servis_id;
                                        $ucretlendirilen->save();
                                    }
                                    BackendController::HatirlatmaEkle(7, $netsiscariid, $depogelen->servis_id, $depogelen->adet);
                                    BackendController::BildirimEkle(6, $netsiscariid, $depogelen->servis_id, $depogelen->adet);
                                    continue;
                                }else{
                                    $depogelen->kod = ServisStokKod::where('stokkodu', $depogelen->servisstokkodu)->first();
                                    $biten = 0;
                                    foreach ($sayacgelenlist as $girilecek) {
                                        if ($girilecek['stokkodu'] == $depogelen->servisstokkodu) {
                                            $serino = $girilecek['serino'];
                                            if ($serino != "") {
                                                try {
                                                    $sayacgelen = new SayacGelen;
                                                    $sayacgelen->depogelen_id = $depogelen->id;
                                                    $sayacgelen->netsiscari_id = $girilecek['netsiscari_id'];
                                                    $sayacgelen->stokkodu = $depogelen->servisstokkodu;
                                                    $sayacgelen->serino = $serino;
                                                    $sayacgelen->depotarihi = $depogelen->tarih;
                                                    $sayacgelen->sayacadi_id = $girilecek['sayacadi_id'];;
                                                    $sayacgelen->sayaccap_id = $girilecek['sayaccap_id'];
                                                    $sayacgelen->uretimyer_id = $girilecek['uretimyer_id'];
                                                    $sayacgelen->servis_id = $servisid;
                                                    $sayacgelen->kullanici_id = Auth::user()->id;
                                                    $sayacgelen->sokulmenedeni = $girilecek['sokulmenedeni'];
                                                    $sayacgelen->beyanname = -2;
                                                    $sayacgelen->save();

                                                } catch (Exception $e) {
                                                    Log::error($e);
                                                    DB::rollBack();
                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayac Kayıdı Yapılamadı', 'text' => 'Sayac Kayıdı Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                                }
                                                try {
                                                    $biten++;
                                                    $subeservistakip=ServisTakip::whereIn('sayacgelen_id',$secilenlist)->where('serino',$sayacgelen->serino)->first();
                                                    $servistakip = new ServisTakip;
                                                    $servistakip->serino = $sayacgelen->serino;
                                                    $servistakip->sayacadi_id = $sayacgelen->sayacadi_id;
                                                    $servistakip->depogelen_id = $sayacgelen->depogelen_id;
                                                    $servistakip->netsiscari_id = $sayacgelen->netsiscari_id;
                                                    $servistakip->sayacgelen_id = $sayacgelen->id;
                                                    $servistakip->servis_id = $sayacgelen->servis_id;
                                                    $servistakip->uretimyer_id = $sayacgelen->uretimyer_id;
                                                    $servistakip->durum= 1;
                                                    $servistakip->subedurum = 0;
                                                    $servistakip->subetakip_id=$subeservistakip->id;
                                                    $servistakip->depotarih = $sayacgelen->depotarihi;
                                                    $servistakip->sayacgiristarihi = $sayacgelen->eklenmetarihi;
                                                    $servistakip->kullanici_id = $sayacgelen->kullanici_id;
                                                    $servistakip->sonislemtarihi = $sayacgelen->eklenmetarihi;
                                                    $servistakip->save();
                                                } catch (Exception $e) {
                                                    Log::error($e);
                                                    DB::rollBack();
                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayac Kayıdı Yapılamadı', 'text' => 'Servis Takip Kısmına Kayıt Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                                }
                                            }
                                        }
                                    }
                                    if ($biten > 0) {
                                        try {
                                            BackendController::HatirlatmaGuncelle(2,$netsiscariid,$servisid,$biten,$depogelen->id,$depogelen->servisstokkodu);
                                            BackendController::DepoDurumGuncelle($depogelen->id);
                                            BackendController::HatirlatmaEkle(3, $netsiscariid, $servisid, $biten,$depogelen->id,$depogelen->servisstokkodu);
                                            BackendController::BildirimEkle(2, $netsiscariid, $servisid, $biten,$depogelen->id,$depogelen->servisstokkodu);
                                        }catch (Exception $e){
                                            Log::error($e);
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayac Kayıdı Yapılamadı', 'text' => 'Hatırlatma Kayıdı Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                        }
                                    }
                                }
                            } else {
                                DB::rollBack();
                                Input::flash();
                                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Ambar Girişi Kaydedilemedi', 'text' => 'Depo Gelen Bilgisi Kaydedilmemiş', 'type' => 'error'));
                            }
                        }
                    } catch (Exception $e) {
                        Log::error($e);
                        DB::rollBack();
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Teslimatı yapılamadı', 'text' => 'Sayaçlar Diğer Depoya Aktarılamadı.', 'type' => 'error'));
                    }
                    foreach ($sayacgelenler as $sayacgelen){
                        BackendController::HatirlatmaSil(3, $netsiscariid, 6, 1,$sayacgelen->depogelen_id,$sayacgelen->stokkodu);
                        BackendController::HatirlatmaGuncelle(11,$netsiscariid,6,1,$sayacgelen->depogelen_id,$sayacgelen->stokkodu);
                    }
                    BackendController::BildirimEkle(11,$netsiscariid, 6, count($sayacgelenlist));
                    BackendController::IslemEkle(1,Auth::user()->id,'label-success','fa-truck',$depolararasi->id.' Numaralı Depolararası Transfer Yapıldı.','Ekleyen:' . Auth::user()->adi_soyadi.',Depolararası Transfer Numarası:'.$depolararasi->id);
                    DB::commit();
                    if($durum==0)
                        return Redirect::to('depo/depolararasi')->with(array('mesaj' => 'true', 'title' => 'Depolararası Transfer Başarıyla Yapıldı', 'text' => 'Faturası kesilmeden gönderildi', 'type' => 'success'));
                    else
                        return Redirect::to('depo/depolararasi')->with(array('mesaj' => 'true', 'title' => 'Depolararası Transfer Başarıyla Yapıldı', 'text' => 'Faturası kesilerek transfer gerçekleşti', 'type' => 'success'));
                }else{
                    DB::rollBack();
                    if($durum==-1){
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Fatura Netsise kaydedilemedi', 'type' => 'error'));
                    }else if($durum==2){
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Fatura Açıklaması Kaydedilemedi', 'type' => 'error'));
                    }else if($durum==3){
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Fatura Genel Bilgileri Kaydedilemedi', 'type' => 'error'));
                    }else if($durum==4){
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Fatura Kalemleri Kaydedilemedi', 'type' => 'error'));
                    }else if($durum==5){
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Bu Fatura Numarası Sistemde Kayıtlı', 'type' => 'error'));
                    }else if($durum== 6){
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Fatura Numarası Kaydedilemedi', 'type' => 'error'));
                    }else{
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => str_replace("'","\'",$durum), 'type' => 'error'));
                    }
                }
            }else{ //bazıları seçilmiş
                try {
                    $kalanlar = BackendController::getListeFark($tumu, $secilenlist);
                    $kalanlist = explode(',', $kalanlar);
                    $kalansayi = count($kalanlist);
                    $depolararasi->secilenler = $kalanlar;
                    $depolararasi->sayacsayisi = $kalansayi;
                    $depolararasi->save();
                } catch (Exception $e) {
                    Log::error($e);
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Teslimatı yapılamadı', 'text' => 'Depolararası Bilgisi Güncellenemedi.', 'type' => 'error'));
                }
                try {
                    $yenidepolararasi = new Depolararasi;
                    $yenidepolararasi->servis_id=$depolararasi->servis_id;
                    $yenidepolararasi->netsiscari_id = $netsiscariid;
                    $yenidepolararasi->secilenler = $secilenler;
                    $yenidepolararasi->sayacsayisi = count($secilenlist);
                    $yenidepolararasi->depodurum = 1;
                    $yenidepolararasi->tipi = $depolararasi->tipi;
                    $yenidepolararasi->depokodu = $depolararasi->depokodu;
                    $yenidepolararasi->kullanici_id = Auth::user()->id;
                    $yenidepolararasi->teslimtarihi = date('Y-m-d H:i:s');
                    $yenidepolararasi->faturano = $faturano;
                    $yenidepolararasi->carikod = $netsiscari->carikod;
                    $yenidepolararasi->ozelkod = '0';
                    $yenidepolararasi->plasiyerkod = $yetkili->plasiyerkod;
                    $yenidepolararasi->aktarilandepo = $aktarilandepo;
                    $yenidepolararasi->netsiskullanici = $yetkili->netsiskullanici;
                    $yenidepolararasi->faturaadres = $aktarilandepo ? $aktarilandepocari->adres . ' ' . $aktarilandepocari->ilce . ' ' . $aktarilandepocari->il : "";
                    $yenidepolararasi->teslimadres = $adres;
                    $yenidepolararasi->save();
                    foreach ($sayacgelenler as $sayacgelen){
                        $servistakip=ServisTakip::where('sayacgelen_id',$sayacgelen->id)->first();
                        $servistakip->depolararasi_id=$depolararasi->id;
                        $servistakip->depolararasitarihi=$depolararasi->teslimtarihi;
                        if($depolararasi->tipi==1)
                            $servistakip->durum=11;
                        else
                            $servistakip->durum=12;
                        $servistakip->kullanici_id = Auth::user()->id;
                        $servistakip->save();
                        $sayacgelen->teslimdurum = $sayacgelen->teslimdurum==2 ? 2 : 1; //şube geri gonderilen sayac 2 teslim edilen 1 //0 bekliyor 1 teslimat 2 geri gönderim 3 hurda 4 depolararasi 5 periyodik bakım
                        $sayacgelen->save();
                    }
                } catch (Exception $e) {
                    Log::error($e);
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Teslimatı yapılamadı', 'text' => 'Yeni Depolararası Bilgisi Eklenemedi.', 'type' => 'error'));
                }
                if ($fatura == 0) //fatura basılmayacaksa
                {
                    $durum = 0;
                    $faturakalemler = array();
                    $teslimdurum=array();
                } else {
                    $eklenenler = BackendController::NetsisSubeDepolararasi($yenidepolararasi->id);
                    $faturakalemler = $eklenenler['kalemler'];
                    $teslimdurum=$eklenenler['teslimdurum'];
                    $durum = $eklenenler['durum'];
                }
                if ($durum == 0 || $durum == 1) {
                    try {
                        foreach ($sayacgelenler as $sayacgelen) {

                            $sayacgelen->depolararasi = 1;
                            $sayacgelen->save();
                        }
                    } catch (Exception $e) {
                        Log::error($e);
                        DB::rollBack();
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Teslimatı yapılamadı', 'text' => 'Sayaç Gelen Bilgisi Güncellenemedi.', 'type' => 'error'));
                    }
                    try {
                        $i=0;
                        foreach ($faturakalemler as $inckey) {
                            $dbname = 'MANAS' . date('Y');
                            $depogelen = DepoGelen::where('db_name', $dbname)->where('inckeyno', $inckey)->first();
                            $now = time();
                            while ($now + 30 > time()) {
                                $depogelen = DepoGelen::where('db_name', $dbname)->where('inckeyno', $inckey)->first();
                                if ($depogelen)
                                    break;
                            }
                            if ($depogelen) {
                                $servisid=$depogelen->servis_id;
                                if($teslimdurum[$i]==2){ // //şubeden geri gönderilen sayaçlar için işlem yapılmayacak eski parçaları takılıp gönderilecek ya da hurda ayrılacak

                                    $hatirlatma = Hatirlatma::where('hatirlatmatip_id', 2)->where('servisstokkodu', $depogelen->servisstokkodu)
                                        ->where('depogelen_id', $depogelen->id)->where('tip', 2)->first();
                                    $hatirlatma->durum = 1;
                                    $hatirlatma->kalan = 0;
                                    $hatirlatma->save();
                                    $depogelen = DepoGelen::where('db_name', $dbname)->where('inckeyno', $inckey)->first();
                                    $depogelen->durum=2;
                                    $depogelen->save();
                                    foreach ($sayacgelenler as $sayacgelen){
                                        $servistakip=ServisTakip::where('sayacgelen_id',$sayacgelen->id)->first();
                                        $ucretlendirilen=Ucretlendirilen::find($servistakip->ucretlendirilen_id);
                                        $ucretlendirilen->servisdurum=$sayacgelen->servis_id;
                                        $ucretlendirilen->save();
                                    }
                                    continue;
                                }else {
                                    $depogelen->kod = ServisStokKod::where('stokkodu', $depogelen->servisstokkodu)->first();
                                    $biten = 0;
                                    foreach ($sayacgelenlist as $girilecek) {
                                        if ($girilecek['stokkodu'] == $depogelen->servisstokkodu) {
                                            $serino = $girilecek['serino'];
                                            if ($serino != "") {
                                                try {
                                                    $sayacgelen = new SayacGelen;
                                                    $sayacgelen->depogelen_id = $depogelen->id;
                                                    $sayacgelen->netsiscari_id = $girilecek['netsiscari_id'];
                                                    $sayacgelen->stokkodu = $depogelen->servisstokkodu;
                                                    $sayacgelen->serino = $serino;
                                                    $sayacgelen->depotarihi = $depogelen->tarih;
                                                    $sayacgelen->sayacadi_id = $girilecek['sayacadi_id'];;
                                                    $sayacgelen->sayaccap_id = $girilecek['sayaccap_id'];
                                                    $sayacgelen->uretimyer_id = $girilecek['uretimyer_id'];
                                                    $sayacgelen->servis_id = $depogelen->servis_id;
                                                    $sayacgelen->kullanici_id = Auth::user()->id;
                                                    $sayacgelen->sokulmenedeni = $girilecek['sokulmenedeni'];
                                                    $sayacgelen->beyanname = -2;
                                                    $sayacgelen->save();
                                                } catch (Exception $e) {
                                                    Log::error($e);
                                                    DB::rollBack();
                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayac Kayıdı Yapılamadı', 'text' => 'Sayac Kayıdı Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                                }
                                                try {
                                                    $biten++;
                                                    $subeservistakip = ServisTakip::whereIn('sayacgelen_id', $secilenlist)->where('serino', $sayacgelen->serino)->first();
                                                    $servistakip = new ServisTakip;
                                                    $servistakip->serino = $sayacgelen->serino;
                                                    $servistakip->sayacadi_id = $sayacgelen->sayacadi_id;
                                                    $servistakip->depogelen_id = $sayacgelen->depogelen_id;
                                                    $servistakip->netsiscari_id = $sayacgelen->netsiscari_id;
                                                    $servistakip->sayacgelen_id = $sayacgelen->id;
                                                    $servistakip->servis_id = $sayacgelen->servis_id;
                                                    $servistakip->uretimyer_id = $sayacgelen->uretimyer_id;
                                                    $servistakip->durum = 1;
                                                    $servistakip->subedurum = 0;
                                                    $servistakip->subetakip_id = $subeservistakip->id;
                                                    $servistakip->depotarih = $sayacgelen->depotarihi;
                                                    $servistakip->sayacgiristarihi = $sayacgelen->eklenmetarihi;
                                                    $servistakip->kullanici_id = $sayacgelen->kullanici_id;
                                                    $servistakip->sonislemtarihi = $sayacgelen->eklenmetarihi;
                                                    $servistakip->save();
                                                } catch (Exception $e) {
                                                    Log::error($e);
                                                    DB::rollBack();
                                                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayac Kayıdı Yapılamadı', 'text' => 'Servis Takip Kısmına Kayıt Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                                }
                                            }
                                        }
                                    }
                                    if ($biten > 0) {
                                        try {
                                            BackendController::HatirlatmaGuncelle(2,$netsiscariid,$servisid,$biten,$depogelen->id,$depogelen->servisstokkodu);
                                            BackendController::DepoDurumGuncelle($depogelen->id);
                                            BackendController::HatirlatmaEkle(3, $netsiscariid, $servisid, $biten,$depogelen->id,$depogelen->servisstokkodu);
                                            BackendController::BildirimEkle(2, $netsiscariid, $servisid, $biten,$depogelen->id,$depogelen->servisstokkodu);
                                        } catch (Exception $e) {
                                            Log::error($e);
                                            DB::rollBack();
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayac Kayıdı Yapılamadı', 'text' => 'Hatırlatma Kayıdı Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                        }
                                    }
                                }
                            } else {
                                DB::rollBack();
                                Input::flash();
                                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Ambar Girişi Kaydedilemedi', 'text' => 'Depo Gelen Bilgisi Kaydedilmemiş', 'type' => 'error'));
                            }
                        }
                    } catch (Exception $e) {
                        Log::error($e);
                        DB::rollBack();
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayac Kayıdı Yapılamadı', 'text' => 'Aktarılan Sayaç Bilgileri Kaydedilemedi.', 'type' => 'error'));
                    }
                    foreach ($sayacgelenler as $sayacgelen){
                        BackendController::HatirlatmaSil(3, $netsiscariid, 6, 1,$sayacgelen->depogelen_id,$sayacgelen->stokkodu);
                        BackendController::HatirlatmaGuncelle(11, $netsiscariid, 6, 1,$sayacgelen->depogelen_id,$sayacgelen->stokkodu);
                    }
                    BackendController::BildirimEkle(11, $netsiscariid, 6, count($sayacgelenlist));
                    BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-truck', $yenidepolararasi->id . ' Numaralı Depolararası Transfer Yapıldı.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Depolararası Transfer Numarası:' . $yenidepolararasi->id);
                    DB::commit();
                    if ($durum == 0)
                        return Redirect::to('depo/depolararasi')->with(array('mesaj' => 'true', 'title' => 'Depolararası Transfer Başarıyla Yapıldı', 'text' => 'Faturası kesilmeden gönderildi', 'type' => 'success'));
                    else
                        return Redirect::to('depo/depolararasi')->with(array('mesaj' => 'true', 'title' => 'Depolararası Transfer Başarıyla Yapıldı', 'text' => 'Faturası kesilerek transfer gerçekleşti', 'type' => 'success'));

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
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Bu Fatura Numarası Sistemde Kayıtlı', 'type' => 'error'));
                    } else if ($durum == 6) {
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Fatura Numarası Kaydedilemedi', 'type' => 'error'));
                    }  else {
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => $durum, 'type' => 'error'));
                    }
                }
            }
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayac Kayıdı Yapılamadı', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function postSubecikisyap($id){
        try {
            DB::beginTransaction();
            $belge1 = 'HURDA SAYAÇTIR. FATURA EDİLMEYECEKTİR.';
            $secilenler = Input::get('cikissecilenler');
            $secilenlist = explode(',', $secilenler);
            $sayacsayisi = count($secilenlist);
            $depolararasi = Depolararasi::find($id);
            if($depolararasi->depodurum==1){
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Çıkışı Tamamlanamadı!', 'text' => 'Seçilen Teslimattaki Sayaçların Çıkışı Zaten Yapılmış!', 'type' => 'warning'));
            }
            $servisid = $depolararasi->servis_id;
            $netsiscariid = $depolararasi->netsiscari_id;
            $arizafiyatlar = ArizaFiyat::whereIn('sayacgelen_id', $secilenlist)->get();
            $depolararasi->depodurum = 1;
            $depolararasi->kullanici_id = Auth::user()->id;
            $depolararasi->teslimtarihi = date('Y-m-d H:i:s');
            $depolararasi->belge1 = $belge1;
            $depolararasi->db_name = Config::get('database.connections.sqlsrv2.database');
            $depolararasi->save();
            foreach ($arizafiyatlar as $arizafiyat) {
                $sayacgelen = SayacGelen::find($arizafiyat->sayacgelen_id);
                $sayacgelen->depolararasi = 1;
                $sayacgelen->beyanname = -2;
                $sayacgelen->save();
                $servistakip = ServisTakip::where('arizafiyat_id', $arizafiyat->id)->first();
                $servistakip->depolararasi_id = $depolararasi->id;
                $servistakip->durum = 11;
                $servistakip->depoteslimtarihi = $depolararasi->teslimtarihi;
                $servistakip->kullanici_id = Auth::user()->id;
                $servistakip->sonislemtarihi = $depolararasi->teslimtarihi;
                $servistakip->save();
                $sayac = Sayac::find($arizafiyat->sayac_id);
                $sayac->songelistarihi = $sayacgelen->depotarihi;
                $sayac->save();
            }
            BackendController::HatirlatmaGuncelle(11, $netsiscariid, $servisid, $sayacsayisi);
            BackendController::BildirimEkle(11, $netsiscariid, $servisid, $sayacsayisi);
            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-truck', $depolararasi->id . ' Nolu Depo Teslimatı Hurda Olarak Yapıldı.', 'Kayıt Yapan:' . Auth::user()->adi_soyadi . ',Depo Teslimat Numarası:' . $depolararasi->id);
            DB::commit();
            return Redirect::to('depo/depolararasi')->with(array('mesaj' => 'true', 'title' => 'Depo Çıkışı Başarıyla Yapıldı', 'text' => 'Sayaçların Hurda Olarak Depo Çıkışı Yapıldı. Fatura kesilmedi.', 'type' => 'success'));

        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Depo Çıkışı Yapılamadı.', 'text' => 'Depo Çıkışı Yapılırken Hata Oluştu.', 'type' => 'error'));
        }
    }

    public function getAboneteslim($hatirlatma_id=false) {
        if($hatirlatma_id)
            return View::make('depo.aboneteslim',array('hatirlatma_id'=>$hatirlatma_id))->with(array('title'=>'Abone Sayaç Teslim Ekranı'));
        else
            return View::make('depo.aboneteslim')->with(array('title'=>'Abone Sayaç Teslim Ekranı'));
    }

    public function postAboneteslimlist() {
        $hatirlatma_id=Input::get('hatirlatma_id');
        $netsiscari_id=Input::get('netsiscari_id');
        $hatirlatma = false;
        if($hatirlatma_id!="") {
            $hatirlatma = Hatirlatma::find($hatirlatma_id);
        }
        if($hatirlatma){
            $query = AboneTeslim::where('aboneteslim.netsiscari_id',$hatirlatma->netsiscari_id)
                ->select(array("aboneteslim.id","abone.adisoyadi","aboneteslim.sayacsayisi","uretimyer.yeradi","aboneteslim.gdurum","kullanici.adi_soyadi",
                    "aboneteslim.teslimtarihi","aboneteslim.gteslimtarihi","abone.nadisoyadi","uretimyer.nyeradi","aboneteslim.ndurum","kullanici.nadi_soyadi"))
                ->leftjoin("uretimyer", "aboneteslim.uretimyer_id", "=", "uretimyer.id")
                ->leftjoin("abone", "aboneteslim.abone_id", "=", "abone.id")
                ->leftJoin("kullanici", "aboneteslim.kullanici_id", "=", "kullanici.id");
        }else if($netsiscari_id!=""){
            $netsiscarilist=explode(',',$netsiscari_id);
            $query = AboneTeslim::whereIn('aboneteslim.netsiscari_id',$netsiscarilist)
                ->select(array("aboneteslim.id","abone.adisoyadi","aboneteslim.sayacsayisi","uretimyer.yeradi","aboneteslim.gdurum","kullanici.adi_soyadi",
                    "aboneteslim.teslimtarihi","aboneteslim.gteslimtarihi","abone.nadisoyadi","uretimyer.nyeradi","aboneteslim.ndurum","kullanici.nadi_soyadi"))
                ->leftjoin("uretimyer", "aboneteslim.uretimyer_id", "=", "uretimyer.id")
                ->leftjoin("abone", "aboneteslim.abone_id", "=", "abone.id")
                ->leftJoin("kullanici", "aboneteslim.kullanici_id", "=", "kullanici.id");
        }else{
            $query = AboneTeslim::select(array("aboneteslim.id","abone.adisoyadi","aboneteslim.sayacsayisi","uretimyer.yeradi","aboneteslim.gdurum","kullanici.adi_soyadi",
                "aboneteslim.teslimtarihi","aboneteslim.gteslimtarihi","abone.nadisoyadi","uretimyer.nyeradi","aboneteslim.ndurum", "kullanici.nadi_soyadi"))
                ->leftjoin("uretimyer", "aboneteslim.uretimyer_id", "=", "uretimyer.id")
                ->leftjoin("abone", "aboneteslim.abone_id", "=", "abone.id")
                ->leftJoin("kullanici", "aboneteslim.kullanici_id", "=", "kullanici.id");
        }
        return Datatables::of($query)
            ->editColumn('teslimtarihi', function ($model) {
                if($model->teslimtarihi){
                    $date = new DateTime($model->teslimtarihi);
                    return $date->format('d-m-Y');
                }else{
                    return '';
                }
            })
            ->addColumn('islemler', function ($model) {
                return "<a class='btn btn-sm btn-warning detay' href='#detay-goster' data-toggle='modal' data-id='{$model->id}'> Detay </a>";
            })
            ->make(true);
    }

    public function getAboneteslimbilgi() {
        try {
            $id=Input::get('id');
            $aboneteslim = AboneTeslim::find($id);
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

    public function getTeslimbilgi() {
        try {
            $id=Input::get('id');
            $aboneteslim = AboneTeslim::find($id);
            $aboneteslim->abone = Abone::find($aboneteslim->abone_id);
            $aboneteslim->netsiscari = Netsiscari::find($aboneteslim->netsiscari_id);
            $aboneteslim->subeyetkili = SubeYetkili::where('kullanici_id', Auth::user()->id)->where('netsiscari_id', $aboneteslim->netsiscari_id)->where('aktif', 1)->first();
            $faturano = Fatuno::where('SUBE_KODU', $aboneteslim->subeyetkili->subekodu)->where('SERI', $aboneteslim->subeyetkili->belgeonek)->where('TIP', '1')->first();
            $aboneteslim->belgeno = BackendController::FaturaNo($faturano->NUMARA, 1);
            $aboneteslim->kasakod = KasaKod::where('subekodu', $aboneteslim->subeyetkili->subekodu)->get();
            if ($aboneteslim->teslimtarihi)
                $aboneteslim->teslimtarihi = date('d-m-Y', strtotime($aboneteslim->teslimtarihi));
            else
                $aboneteslim->teslimtarihi = "";
            $secilenler = explode(',', $aboneteslim->secilenler);
            $aboneteslim->sayacgelen = SayacGelen::whereIn('id', $secilenler)->get();
            $aboneteslim->parabirimi = ParaBirimi::find($aboneteslim->parabirimi_id);
            $aboneteslim->parabirimi2 = ParaBirimi::find($aboneteslim->parabirimi2_id);
            if ($aboneteslim->arizafiyat->count() > 0) {
                foreach ($aboneteslim->sayacgelen as $sayacgelen) {
                    $sayacgelen->sayacadi = SayacAdi::find($sayacgelen->sayacadi_id);
                    $sayacgelen->sayaccap = SayacCap::find($sayacgelen->sayaccap_id);
                    $sayacgelen->uretimyer = UretimYer::find($sayacgelen->uretimyer_id);
                    $sayacgelen->arizafiyat = ArizaFiyat::where('sayacgelen_id',$sayacgelen->id)->first();
                    $sayacgelen->parabirimi = ParaBirimi::find($sayacgelen->arizafiyat->parabirimi_id);
                    $sayacgelen->parabirimi2 = ParaBirimi::find($sayacgelen->arizafiyat->parabirimi2_id);
                    $sayacgelen->dovizkuru = DovizKuru::where('tarih', $sayacgelen->arizafiyat->kurtarihi)->orderBy('parabirimi_id', 'asc')->take(3)->get();
                    if ($sayacgelen->arizafiyat->kurtarihi != null)
                        $sayacgelen->arizafiyat->kurtarihi = date('d-m-Y', strtotime($sayacgelen->arizafiyat->kurtarihi));
                    else
                        $sayacgelen->arizafiyat->kurtarihi = "";
                    foreach ($sayacgelen->arizafiyat->dovizkuru as $doviz) {
                        $doviz->tarih = date("d-m-Y", strtotime($doviz->tarih));
                    }
                    if ($sayacgelen->aboneteslim)
                        $sayacgelen->durum = "Teslim Edildi";
                    else
                        $sayacgelen->durum = "Teslim Edilmedi";
                }
            } else {
                $aboneteslim->sayacgelen = SayacGelen::whereIn("id", $secilenler)->get();
                foreach ($aboneteslim->sayacgelen as $sayacgelen) {
                    $sayacgelen->sayacadi = SayacAdi::find($sayacgelen->sayacadi_id);
                    $sayacgelen->sayaccap = SayacCap::find($sayacgelen->sayaccap_id);
                    $sayacgelen->uretimyer = UretimYer::find($sayacgelen->uretimyer_id);
                    if ($sayacgelen->aboneteslim)
                        $sayacgelen->durum = "Teslim Edildi";
                    else
                        $sayacgelen->durum = "Teslim Edilmedi";
                }
            }
            $teslimadres = AboneTeslim::selectRaw('count(*) AS sayi, faturaadres')->where('netsiscari_id',$aboneteslim->netsiscari_id)->where('teslimdurum',1)->where('faturaadres','<>','')->groupBy('faturaadres')->orderBy('sayi', 'DESC')->get();

            return Response::json(array('durum'=>true,'aboneteslim' => $aboneteslim,'teslimadres' => $teslimadres));
        } catch (Exception $e) {
            return Response::json(array('durum' => false, 'title' => 'Abone Teslimat Bilgisi Getirilemedi', 'text' => str_replace("'","\'",$e->getMessage()),'type'=>'error' ));
        }
    }

    public function postAboneteslimet($id)
    {
        try {
            if (Input::has('faturavar')) {
                $rules = ['teslimfaturano' => 'required', 'teslimaciklama' => 'required', 'teslimkasakod' => 'required', 'teslimadres' => 'required'];
                $validate = Validator::make(Input::all(), $rules);
                $messages = $validate->messages();
                if ($validate->fails()) {
                    Input::flash();
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
                }
                $fatura = 1;
                $faturano = Input::get('teslimfaturano');
            } else {// fatura basılmayacak
                $fatura = 0;
                $faturano = "";
            }
            $secilenler = Input::get('teslimsecilenler');
            $teslimadres = Input::get('teslimadres');
            $fatirsno = BackendController::FaturaNo($faturano, 0);
            $odemesekli = Input::get('teslimodemesekli');
            $aciklama = Input::get('teslimaciklama');
            $kasakodu = Input::get('teslimkasakod');
            $secilenlist = explode(',', $secilenler);
            $aboneteslim = AboneTeslim::find($id);
            if($aboneteslim->teslimdurum==1){
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'abone Teslimi Tamamlanamadı!', 'text' => 'Seçilen Teslimattaki Sayaçların Teslimi Zaten Yapılmış!', 'type' => 'warning'));
            }
            $sayacgelenler = SayacGelen::whereIn('id', $secilenlist)->orderBy('servis_id', 'asc')->get();
            $sayacgelenlist = $sayacgelenler->toArray();
            $stokkodu = array();
            $servisler = array();
            $servis = 0;
            $i = 0;
            foreach ($sayacgelenler as $sayacgelen) {
                $servistakip=ServisTakip::where('sayacgelen_id',$sayacgelen->id)->first();
                if ($servis != $sayacgelen->servis_id) {
                    if ($servis != 0) {
                        array_push($servisler, array('servis' => $servis, 'adet' => $i));
                        $servis = $servistakip->subedurum ? 6 : $sayacgelen->servis_id;
                        $i = 1;
                    } else {
                        $i++;
                        $servis = $servistakip->subedurum ? 6 : $sayacgelen->servis_id;
                    }
                }
            }
            if ($i > 0)
                array_push($servisler, array('servis' => $servis, 'adet' => $i));
            foreach ($sayacgelenlist as $key => $row) {
                $stokkodu[$key] = $row['stokkodu'];
            }
            array_multisort($stokkodu, SORT_ASC, $sayacgelenlist);
            $netsiscari = NetsisCari::find($aboneteslim->netsiscari_id);
            if ($netsiscari->caridurum != "A")
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Netsis Cari Uyarısı', 'text' => 'Cari Bilgisi Netsis Üzerinde Kilitli.', 'type' => 'warning'));
            $subeyetkili = SubeYetkili::where('netsiscari_id', $aboneteslim->netsiscari_id)->where('aktif',1)->first();
            if (!$subeyetkili) {
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Şube Yetkili Uyarısı', 'text' => 'Bu Yere Ait Şube Yetkilisi Yok.', 'type' => 'warning'));
            }
            $yetkili = ServisYetkili::where('kullanici_id', $subeyetkili->kullanici_id)->first();
            if (!$yetkili) {
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kullanıcı Yetki Uyarısı', 'text' => 'Bu Kullanıcının Yetkisi Yok.', 'type' => 'warning'));
            }
            DB::beginTransaction();
            try {
                $arizafiyatlar = ArizaFiyat::whereIn('sayacgelen_id', $secilenlist)->get();
                $toplamtutar = 0;
                $kurtarihi="";
                $birim = 1; //tüm çıkışlar tl cinisinden olacak
                foreach ($arizafiyatlar as $arizafiyat) {
                    $kurtarihi=$arizafiyat->kurtarihi;
                    $kur = 1;
                    if ($arizafiyat->parabirimi_id != 1) {
                        $dovizkuru = DovizKuru::where('parabirimi_id', $arizafiyat->parabirimi_id)->where('tarih', $kurtarihi)->first();
                        if (!$dovizkuru){
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'text' => 'Kur Fiyatı Bulunamadı', 'type' => 'warning', 'title' => 'Kur Hatası'));
                        }
                        $kur = $dovizkuru->kurfiyati;
                    }
                    $arizafiyat->fiyat = $arizafiyat->fiyat * $kur;
                    if ($arizafiyat->parabirimi2_id == $birim) {
                        $arizafiyat->fiyat += $arizafiyat->fiyat2;
                        $arizafiyat->fiyat2 = 0;
                        $arizafiyat->parabirimi2_id = null;
                    }
                    if($arizafiyat->parabirimi2_id!=null){
                        $dovizkuru = DovizKuru::where('parabirimi_id', $arizafiyat->parabirimi2_id)->where('tarih', date('Y-m-d'))->first();
                        if (!$dovizkuru){
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'text' => 'Bugüne ait Kur Fiyatı Bulunamadı', 'type' => 'warning', 'title' => 'Kur Hatası'));
                        }
                        $kur = $dovizkuru->kurfiyati;
                        $arizafiyat->fiyat += $arizafiyat->fiyat2 * $kur;
                        $arizafiyat->fiyat2 = 0;
                        $arizafiyat->parabirimi2_id = null;
                    }
                    $indirim = $arizafiyat->indirim == 1 ? ($arizafiyat->fiyat * $arizafiyat->indirimorani) / 100 : 0;
                    $arizafiyat->tutar = $arizafiyat->fiyat - $indirim;
                    $arizafiyat->kdv = ($arizafiyat->tutar * 18) / 100;
                    $arizafiyat->toplamtutar = round(($arizafiyat->tutar + $arizafiyat->kdv) * 2) / 2;
                    $arizafiyat->parabirimi_id = $birim;
                    $arizafiyat->save();
                    $toplamtutar += $arizafiyat->toplamtutar;
                }
                $kdv = round(($toplamtutar * 18) / 118, 2);
                $tutar=$toplamtutar-$kdv;
                $kdvorani = 18;
                $yazitutar = BackendController::YaziTutar(number_format($toplamtutar, 2,'.',''), 1);

                $aboneteslim->tutar=$tutar;
                $aboneteslim->kdv=$kdv;
                $aboneteslim->toplamtutar=$toplamtutar;
                $aboneteslim->kdvorani=$kdvorani;
                $aboneteslim->yazitutar= $yazitutar;
                $aboneteslim->parabirimi_id=$birim;
                $aboneteslim->parabirimi2_id=null;
                $aboneteslim->teslimdurum = 1;
                $aboneteslim->kurtarihi = $kurtarihi;
                $aboneteslim->kullanici_id = Auth::user()->id;
                $aboneteslim->teslimtarihi = date('Y-m-d H:i:s');
                $aboneteslim->db_name = Config::get('database.connections.sqlsrv2.database');
                $aboneteslim->faturano = $fatirsno;
                $aboneteslim->faturaadres = $teslimadres;
                $aboneteslim->carikod = $netsiscari->carikod;
                $aboneteslim->ozelkod = $yetkili->ozelkod;
                $aboneteslim->projekodu = $subeyetkili->projekodu;
                $aboneteslim->plasiyerkod = $yetkili->plasiyerkod;
                $aboneteslim->depokodu = $yetkili->depokodu;
                $aboneteslim->subekodu = $subeyetkili->subekodu;
                $aboneteslim->aciklama = $aciklama;
                $aboneteslim->odemesekli = $odemesekli;
                $aboneteslim->kasakodu = $kasakodu;
                $aboneteslim->netsiskullanici = $yetkili->netsiskullanici;
                $aboneteslim->netsiskullanici_id = $yetkili->netsiskullanici_id;
                $aboneteslim->save();
            } catch (Exception $e) {
                Log::error($e);
                DB::rollBack();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Abone Teslim Bilgisi Kaydedilemedi', 'text' => 'Abone Sayaç Teslimi Kaydedilirken Hata Oluştu.', 'type' => 'warning'));
            }
            if ($fatura == 0) //fatura basılmayacaksa
            {
                $durum = 0;
            } else {
                Fatuno::where(['SUBE_KODU'=>$subeyetkili->subekodu,'SERI' => $subeyetkili->belgeonek, 'TIP' => '1'])->update(['NUMARA' => $fatirsno]);
                $durum = BackendController::AboneTeslimFaturasi($id);
            }
            if ($durum < 2) {
                try {
                    foreach ($sayacgelenler as $sayacgelen) {
                        $sayacgelen->aboneteslim = 1;
                        $sayacgelen->save();
                    }
                    foreach ($servisler as $servis) {
                        BackendController::HatirlatmaGuncelle(12, $aboneteslim->netsiscari_id, $servis['servis'], $servis['adet']);
                        BackendController::BildirimEkle(12, $aboneteslim->netsiscari_id, $servis['servis'], $servis['adet']);
                    }
                } catch (Exception $e) {
                    Log::error($e);
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Abone Teslim Bilgisi Kaydedilemedi', 'text' => 'Sayaçların Gelen Bilgisi Güncellenemedi.', 'type' => 'warning'));
                }
                try {
                    foreach ($sayacgelenler as $sayacgelen) {
                        $servistakip = ServisTakip::where('sayacgelen_id', $sayacgelen->id)->first();
                        $servistakip->aboneteslim_id = $aboneteslim->id;
                        $servistakip->durum = 13;
                        $servistakip->aboneteslimtarihi = $aboneteslim->teslimtarihi;
                        $servistakip->sonislemtarihi = $aboneteslim->teslimtarihi;
                        $servistakip->save();
                    }
                } catch (Exception $e) {
                    Log::error($e);
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Abone Teslim Bilgisi Kaydedilemedi', 'text' => 'Sayaçların Servis Takip Bilgisi Güncellenemedi.', 'type' => 'warning'));
                }
                BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-user', $aboneteslim->id . ' Numaralı Abone Sayaç Teslimi Yapıldı.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Abone Sayaç Teslim Numarası:' . $aboneteslim->id);
                DB::commit();
                if ($durum == 0) {
                    return Redirect::to('depo/aboneteslim')->with(array('mesaj' => 'true', 'title' => 'Sayaç Teslimi Başarıyla Yapıldı', 'text' => 'Faturası oluşturulmadan işlem yapıldı', 'type' => 'success'));
                } else {
                    return Redirect::to('depo/aboneteslim')->with(array('mesaj' => 'true', 'title' => 'Sayaç Teslimi Başarıyla Yapıldı', 'text' => 'Faturası oluşturularak işlem yapıldı', 'type' => 'success'));
                }
            } else {
                DB::rollBack();
                if ($durum == 2) {
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Fatura Netsise kaydedilemedi', 'type' => 'error'));
                } else if ($durum == 3) {
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Fatura Açıklaması Kaydedilemedi', 'type' => 'error'));
                } else if ($durum == 4) {
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Fatura Kalemleri Kaydedilemedi', 'type' => 'error'));
                } else if ($durum == 5) {
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Bugünün Döviz Kuru Netsisten Alınamadı', 'type' => 'error'));
                } else if ($durum == 6) {
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Bu Fatura Numarası Sistemde Kayıtlı', 'type' => 'error'));
                } else if ($durum == 7) {
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Fatura Kalemleri Kaydedilemedi', 'type' => 'error'));
                } else if ($durum == 8) {
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Ücret Kasaya Kaydedilemedi', 'type' => 'error'));
                } else if ($durum == 9) {
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Servis Bilgisi Kaydedilemedi', 'type' => 'error'));
                } else if ($durum == 10) {
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Sayaç Aboneye Ait Değil!', 'type' => 'error'));
                } else if ($durum == 11) {
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Abone Sayacı Bulunamadı!', 'type' => 'error'));
                } else if ($durum == 12) {
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => 'Abonenin Kayıtlı Olduğu Şube Bulunamadı!', 'type' => 'error'));
                } else{
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Faturası kesilemedi', 'text' => $durum, 'type' => 'error'));
                }
            }
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Abone Teslim Bilgisi Kaydedilemedi', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'warning'));
        }
    }

    public function postAboneteslim()
    {
        try {
            if (Input::has('irsaliye')) {
                $id = Input::get('irsaliye');
                $aboneteslim = AboneTeslim::find($id);
                if ($aboneteslim) {
                    if (!$aboneteslim->teslimdurum){
                        return Redirect::back()->with(array('mesaj' => 'true', 'text' => 'Sayaçlar Teslim Edilmeden İrsaliye Çıkarılamaz!', 'type' => 'warning', 'title' => 'İrsaliye Hatası'));
                    }
                    if ($aboneteslim->faturano == "") {
                        return Redirect::back()->with(array('mesaj' => 'true', 'text' => 'Faturası Kaydedilmeden Çıkış Yapılmış', 'type' => 'warning', 'title' => 'Fatura Hatası'));
                    }
                    /*$aboneteslim->abone = Abone::find($aboneteslim->abone_id);
                    $secilenler = explode(',', $aboneteslim->secilenler);
                    $arizafiyatlar = ArizaFiyat::whereIn('sayacgelen_id', $secilenler)->get();
                    $kurtarihi = "";
                    foreach ($arizafiyatlar as $arizafiyat) {
                        $depogelen = DepoGelen::find($arizafiyat->depogelen_id);
                        $arizafiyat->servisstokkodu = $depogelen->servisstokkodu;
                        $servisstokkod = ServisStokKod::where('stokkodu', $depogelen->servisstokkodu)->first();
                        $arizafiyat->stokadi = $servisstokkod->stokadi;
                        $arizafiyat->adet = 1;
                        $kurtarihi = $arizafiyat->kurtarihi;
                    }
                    $arizafiyatlist = $arizafiyatlar->toArray();
                    $urunler = BackendController::FaturaGrupla($arizafiyatlist);
                    $tutar = 0;
                    foreach ($urunler as $kalem) {
                        $parabirimi = $kalem['parabirimi_id'];
                        $adet = $kalem['adet'];
                        if ($parabirimi != 1) {
                            $dovizkuru = DovizKuru::where('parabirimi_id', $parabirimi)->where('tarih', $kurtarihi)->first();
                            if (!$dovizkuru)
                                return Redirect::back()->with(array('mesaj' => 'true', 'text' => 'Kur Fiyatı Bulunamadı', 'type' => 'warning', 'title' => 'Kur Hatası'));
                            $kur = $dovizkuru->kurfiyati;
                            $doviztutar = $kalem['toplamtutar'];
                            $kalemtutar = $doviztutar * $kur;
                        } else {
                            $kalemtutar = $kalem['toplamtutar'];
                        }
                        $tutar += ($adet * $kalemtutar);
                    }*/
                    $raporadi = "AboneTeslim-" . Str::slug($aboneteslim->abone->adisoyadi);
                    $export = "pdf";
                    $kriterler = array("id"=>$id);

                    JasperPHP::process(public_path('reports/aboneteslimirsaliye/aboneteslimirsaliye.jasper'), public_path('reports/outputs/aboneteslimirsaliye/' . $raporadi),
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
                    readfile("reports/outputs/aboneteslimirsaliye/" . $raporadi . "." . $export . "");
                    File::delete("reports/outputs/aboneteslimirsaliye/" . $raporadi . "." . $export . "");
                    return Redirect::back()->with(array('mesaj' => 'false'));
                } else {
                    return Redirect::back()->with(array('mesaj' => 'true', 'text' => 'Rapor alınacak teslimat seçilmedi', 'type' => 'warning', 'title' => 'Rapor Hatası'));
                }
            } else { //liste
                $id = Input::get('irsaliyeek');
                $aboneteslim = AboneTeslim::find($id);
                if ($aboneteslim) {
                    if (!$aboneteslim->teslimdurum){
                        return Redirect::back()->with(array('mesaj' => 'true', 'text' => 'Sayaçlar Teslim Edilmeden Sayaç Listesi Çıkarılamaz!', 'type' => 'warning', 'title' => 'Rapor Hatası'));
                    }
                    $aboneteslim->abone = Abone::find($aboneteslim->abone_id);
                    $raporadi = "SayacListesi-" . Str::slug($aboneteslim->abone->adisoyadi);
                    $export = "pdf";
                    $kriterler = array("id"=>$id);

                    JasperPHP::process(public_path('reports/aboneteslimsayaclistesi/aboneteslimsayaclistesi.jasper'), public_path('reports/outputs/aboneteslimsayaclistesi/' . $raporadi),
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
                    readfile("reports/outputs/aboneteslimsayaclistesi/" . $raporadi . "." . $export . "");
                    File::delete("reports/outputs/aboneteslimsayaclistesi/" . $raporadi . "." . $export . "");
                    return Redirect::back()->with(array('mesaj' => 'false'));
                } else {
                    return Redirect::back()->with(array('mesaj' => 'true', 'text' => 'Sayaç Listesi alınacak teslimat seçilmedi', 'type' => 'warning', 'title' => 'Rapor Hatası'));
                }
            }
        } catch (Exception $e) {
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Rapor Hatası', 'text' => str_replace("'","\'",$e->getMessage()) , 'type' => 'error'));
        }
    }

}
