<?php
use Jenssegers\Agent\Agent;
class UretimController extends BackendController {
    public $servisadi = 'uretim';
    public $servisbilgi = 'Üretim';

    public function getAcikisemri() {
        return View::make($this->servisadi.'.acikisemri')->with(array('title'=>$this->servisbilgi.' Açık İş Emirleri'));
    }

    public function postAcikisemrilist() {
        $query = AcikIsEmri::select(array(DB::raw('dbo.TRK(ISEMRINO) as ISEMRINO'),DB::raw('dbo.TRK(STOK_KODU) as STOK_KODU'),DB::raw('dbo.TRK(STOK_ADI) as STOK_ADI'),"TARIH","MIKTAR","URETILENMIKTAR","KALANMIKTAR"));

        return Datatables::of($query)
            ->editColumn('TARIH', function ($model) {
                $date = new DateTime($model->TARIH);
                return $date->format('d-m-Y');})
            ->editColumn('MIKTAR', function ($model) {
                return intval($model->MIKTAR);})
            ->editColumn('URETILENMIKTAR', function ($model) {
                return intval($model->URETILENMIKTAR);})
            ->editColumn('KALANMIKTAR', function ($model) {
                return intval($model->KALANMIKTAR);})
            ->addColumn('islemler',function ($model){
                return "<a class='btn btn-sm btn-warning detay' href='#isemridetay' data-toggle='modal' data-id='{$model->ISEMRINO}'> Detay </a>";
            })
            ->make(true);
    }

    public function getIsemridetay() {
        try {
            $isemrino = Input::get('isemrino');
            $isemri = IsEmri::where('ISEMRINO',BackendController::ReverseTrk($isemrino))->first();
            $isemri->MIKTAR = intval($isemri->MIKTAR);

            $uretilen = StokUrs::where('URETSON_SIPNO',BackendController::ReverseTrk($isemrino))
                ->where('URETSON_MAMUL',$isemri->STOK_KODU)->where('KAYIT_EDILSIN',1)
                ->groupBy('URETSON_SIPNO','URETSON_MAMUL')->first((array(DB::raw('SUM(URETSON_MIKTAR) AS URETILENMIKTAR'))));
            if($uretilen)
                $isemri->URETILENMIKTAR = intval($uretilen->URETILENMIKTAR);
            else
                $isemri->URETILENMIKTAR = 0;
            $isemri->KALANMIKTAR = $isemri->MIKTAR - $isemri->URETILENMIKTAR;
            $netsisstokkod = NetsisStokKod::where('kodu',$isemri->STOK_KODU)->first();
            $isemri->CARI_ISIM='';
            if(!is_null($isemri->SIPARIS_NO)){
                $sipatra = Sipatra::where('FISNO',$isemri->SIPARIS_NO)->first(DB::raw('dbo.TRK(TBLSIPATRA.STHAR_CARIKOD) as STHAR_CARIKOD'));
                if($sipatra){
                    $netsiscari = NetsisCari::where('carikod',$sipatra->STHAR_CARIKOD)->first();
                    $isemri->CARI_ISIM=$netsiscari->cariadi;
                }
            }
            $isemri->STOK_ADI = $netsisstokkod->adi;
            $date = new DateTime($isemri->TARIH);
            $isemri->TARIH = $date->format('d-m-Y');
            $date = new DateTime($isemri->TESLIM_TARIHI);
            $isemri->TESLIM_TARIHI = $date->format('d-m-Y');
            return Response::json(array('durum'=>true,'isemri' => $isemri));
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum' => false, 'title' => 'Hatalı Bilgi Mevcut', 'text' => str_replace("'","\'",$e->getMessage()), 'type'=>'error'));
        }
    }

    public function getUrunkayit() {
        $agent = new Agent();
        $mobile = $agent->isMobile();
        return View::make($this->servisadi.'.urunkayit')->with(array('mobile'=>$mobile))->with(array('title'=>$this->servisbilgi.' Ürün Kayıdı'));
    }

    public function postUrunkayitlist() {
            $query = UretimUrun::select(array("uretimurun.id","uretimurun.urunadi","netsisstokkod.kodu","uretimuretici.ureticiadi","uretimurunmarka.markaadi","uretimurun.adet",
                "uretimurun.depotarihi","uretimurun.eklenmetarihi","uretimurun.gdepotarihi","uretimurun.geklenmetarihi","uretimurun.nurunadi",
                "netsisstokkod.nkodu","uretimuretici.nureticiadi","uretimurunmarka.nmarkaadi","uretimurun.kalan"))
                ->leftjoin("netsisstokkod", "uretimurun.netsisstokkod_id", "=", "netsisstokkod.id")
                ->leftjoin("uretimuretici", "uretimurun.uretimuretici_id", "=", "uretimuretici.id")
                ->leftjoin("uretimurunmarka", "uretimurun.uretimurunmarka_id", "=", "uretimurunmarka.id");

        return Datatables::of($query)
            ->editColumn('depotarihi', function ($model) {
                $date = new DateTime($model->depotarihi);
                return $date->format('d-m-Y');})
            ->editColumn('eklenmetarihi', function ($model) {
                $date = new DateTime($model->eklenmetarihi);
                return $date->format('d-m-Y');})
            ->addColumn('islemler',function ($model) {
                $root = BackendController::getRootDizin();
                if(intval($model->kalan)>0)
                    return "<a class='btn btn-sm btn-warning' href='".$root."/".$this->servisadi."/urunkayitduzenle/".$model->id."' > Düzenle </a>
                        <a href='#portlet-delete' data-toggle='modal' data-id='".$model->id."' class='btn btn-sm btn-danger delete' data-original-title='' title=''>Sil</a>";
                else
                    return "<a class='btn btn-sm btn-info' href='".$root."/".$this->servisadi."/urunkayitgoster/".$model->id."' > Göster </a>";
            })
            ->make(true);
    }

    public function getUrunkayitekle() {
        $ureticiler = UretimUretici::all();
        $markalar = UretimUrunMarka::all();
        $netsisstokkodlari=NetsisStokKod::where('grupkodu','MALZEME')->get();
        return View::make($this->servisadi.'.urunkayitekle',array('ureticiler'=>$ureticiler,'markalar'=>$markalar,'netsisstokkodlari'=>$netsisstokkodlari))->with(array('title'=>$this->servisbilgi.' Ürün Kayıdı Ekle'));
    }

    public function getUrunkayitliste()
    {
        try {
            $netsisstokkodlari=NetsisStokKod::where('grupkodu','MALZEME')->get();
            $ureticiler=UretimUretici::all();
            $markalar=UretimUrunMarka::all();
            return Response::json(array('durum' => true, 'netsisstokkodlari' => $netsisstokkodlari, 'ureticiler' => $ureticiler, 'markalar' => $markalar));
        } catch (Exception $e) {
            return Response::json(array('durum' => false, 'title' => 'Ürün Kayıt Bilgi Getirme Hatası', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getUreticistokkod()
    {
        try {
            $stokkodu=Input::get('stokkod');
            $netsisstokkod = NetsisStokKod::find($stokkodu);
            $ureticiler=UretimUretici::all();
            $dbname = 'MANAS' . date('Y');
            $uretimurunler = UretimUrun::where('db_name',$dbname)->where('netsisstokkod_id',$stokkodu)->get(array('inckeyno'))->toArray();
            $depokayitlari = Sthar::where('TBLSTHAR.STHAR_GCKOD','G')->where(function ($query) {
                $query->whereIn('TBLSTHAR.STHAR_FTIRSIP',array(2,4))->orWhereNull('TBLSTHAR.STHAR_FTIRSIP');})->where('TBLSTHAR.DEPO_KODU','1')
                ->where('TBLSTHAR.STOK_KODU',$netsisstokkod->kodu)->whereNotIn('TBLSTHAR.INCKEYNO',$uretimurunler)
                ->leftJoin('TBLCASABIT','TBLCASABIT.CARI_KOD','=','TBLSTHAR.STHAR_CARIKOD')
                ->orderBy('STHAR_TARIH','desc')->get(array(DB::raw('dbo.TRK(TBLSTHAR.STOK_KODU) as STOK_KODU'),'TBLSTHAR.FISNO','TBLSTHAR.STHAR_GCMIK','TBLSTHAR.STHAR_TARIH'
                ,DB::raw('dbo.TRK(TBLSTHAR.STHAR_CARIKOD) as STHAR_CARIKOD'),DB::raw('dbo.TRK(TBLCASABIT.CARI_ISIM) as CARI_ISIM'),'TBLSTHAR.STHAR_SIPNUM'
                ,DB::raw('dbo.TRK(TBLSTHAR.STHAR_ACIKLAMA) as STHAR_ACIKLAMA'),'TBLSTHAR.INCKEYNO'));
            foreach ($depokayitlari as $depokayidi){
                $depokayidi->STHAR_GCMIK = intval($depokayidi->STHAR_GCMIK);
                $date = new DateTime($depokayidi->STHAR_TARIH);
                $depokayidi->STHAR_TARIH =  $date->format('d-m-Y');
            }
            return Response::json(array('durum' => true, 'ureticiler' => $ureticiler,'depokayitlari'=>$depokayitlari));
        } catch (Exception $e) {
            return Response::json(array('durum' => false, 'title' => 'Stok Kodu Hatası', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getUrunkayitbilgi(){
        try {
            $secilenler=explode(',',Input::get('secilenler'));
            $netsisstokkodlari=NetsisStokKod::where('grupkodu','MALZEME')->get();
            $dbname = 'MANAS' . date('Y');
            $durum = false;
            $bilgi=array();
            if(Input::has('urunid')){
                $urunid = Input::get('urunid');
                $uretimurun = UretimUrun::find($urunid);
                if ($uretimurun->db_name != 'MANAS' . date('Y')) { //eski kayit guncelleniyorsa
                    $connection = BackendController::AddNewConnection($uretimurun->db_name);
                    $durum = true;
                    for ($i = 0; $i < count($secilenler); $i++) {
                        $inckeyno = $secilenler[$i];
                        $depokayit = Sthar::on($connection)->where('TBLSTHAR.INCKEYNO', $inckeyno)
                            ->orderBy('STHAR_TARIH', 'desc')->first(array(DB::raw('dbo.TRK(TBLSTHAR.STOK_KODU) as STOK_KODU'), 'TBLSTHAR.FISNO', 'TBLSTHAR.STHAR_GCMIK',
                                'TBLSTHAR.STHAR_TARIH', DB::raw('dbo.TRK(TBLSTHAR.STHAR_CARIKOD) as STHAR_CARIKOD'), 'TBLSTHAR.STHAR_SIPNUM'
                            , DB::raw('dbo.TRK(TBLSTHAR.STHAR_ACIKLAMA) as STHAR_ACIKLAMA'), 'TBLSTHAR.INCKEYNO'));
                        $depokayit->netsisstokkod = NetsisStokKod::where('kodu', $depokayit->STOK_KODU)->first();
                        $bilgi[$i]['kayit'] = $depokayit;
                        $netsisstokkod = NetsisStokKod::where('kodu', $depokayit->STOK_KODU)->first();
                        $uretimurunler = UretimUrun::where('db_name', $uretimurun->db_name)->where('netsisstokkod_id', $netsisstokkod->id)
                            ->where('inckeyno', '<>', $depokayit->INCKEYNO)
                            ->get(array('inckeyno'))->toArray();
                        $bilgi[$i]['depokayitlari'] = Sthar::on($connection)->where('TBLSTHAR.STHAR_GCKOD', 'G')->where(function ($query) {
                            $query->whereIn('TBLSTHAR.STHAR_FTIRSIP', array(2, 4))->orWhereNull('TBLSTHAR.STHAR_FTIRSIP');
                        })->where('TBLSTHAR.DEPO_KODU', 1)
                            ->where('TBLSTHAR.STOK_KODU', $depokayit->STOK_KODU)->whereNotIn('TBLSTHAR.INCKEYNO', $uretimurunler)
                            ->leftJoin('TBLCASABIT', 'TBLCASABIT.CARI_KOD', '=', 'TBLSTHAR.STHAR_CARIKOD')
                            ->orderBy('STHAR_TARIH', 'desc')->get(array(DB::raw('dbo.TRK(TBLSTHAR.STOK_KODU) as STOK_KODU'), 'TBLSTHAR.FISNO', 'TBLSTHAR.STHAR_GCMIK', 'TBLSTHAR.STHAR_TARIH'
                            , DB::raw('dbo.TRK(TBLSTHAR.STHAR_CARIKOD) as STHAR_CARIKOD'), DB::raw('dbo.TRK(TBLCASABIT.CARI_ISIM) as CARI_ISIM'), 'TBLSTHAR.STHAR_SIPNUM'
                            , DB::raw('dbo.TRK(TBLSTHAR.STHAR_ACIKLAMA) as STHAR_ACIKLAMA'), 'TBLSTHAR.INCKEYNO'));
                        foreach ($bilgi[$i]['depokayitlari'] as $depokayidi) {
                            $depokayidi->STHAR_GCMIK = intval($depokayidi->STHAR_GCMIK);
                            $date = new DateTime($depokayidi->STHAR_TARIH);
                            $depokayidi->STHAR_TARIH = $date->format('d-m-Y');
                        }
                        $bilgi[$i]['ureticiler'] = UretimUretici::all();
                        $bilgi[$i]['markalar'] = UretimUrunMarka::all();
                    }
                    BackendController::DeleteConnection($connection);
                }else{
                    for ($i = 0; $i < count($secilenler); $i++) {
                        $inckeyno = $secilenler[$i];
                        $depokayit = Sthar::where('TBLSTHAR.INCKEYNO', $inckeyno)
                            ->orderBy('STHAR_TARIH', 'desc')->first(array(DB::raw('dbo.TRK(TBLSTHAR.STOK_KODU) as STOK_KODU'), 'TBLSTHAR.FISNO', 'TBLSTHAR.STHAR_GCMIK',
                                'TBLSTHAR.STHAR_TARIH', DB::raw('dbo.TRK(TBLSTHAR.STHAR_CARIKOD) as STHAR_CARIKOD'), 'TBLSTHAR.STHAR_SIPNUM'
                            , DB::raw('dbo.TRK(TBLSTHAR.STHAR_ACIKLAMA) as STHAR_ACIKLAMA'), 'TBLSTHAR.INCKEYNO'));
                        $depokayit->netsisstokkod = NetsisStokKod::where('kodu', $depokayit->STOK_KODU)->first();
                        $bilgi[$i]['kayit'] = $depokayit;
                        $netsisstokkod = NetsisStokKod::where('kodu', $depokayit->STOK_KODU)->first();
                        $uretimurunler = UretimUrun::where('db_name', $dbname)->where('netsisstokkod_id', $netsisstokkod->id)
                            ->where('inckeyno', '<>', $depokayit->INCKEYNO)
                            ->get(array('inckeyno'))->toArray();
                        $bilgi[$i]['depokayitlari'] = Sthar::where('TBLSTHAR.STHAR_GCKOD', 'G')->where(function ($query) {
                            $query->whereIn('TBLSTHAR.STHAR_FTIRSIP', array(2, 4))->orWhereNull('TBLSTHAR.STHAR_FTIRSIP');
                        })->where('TBLSTHAR.DEPO_KODU', 1)
                            ->where('TBLSTHAR.STOK_KODU', $depokayit->STOK_KODU)->whereNotIn('TBLSTHAR.INCKEYNO', $uretimurunler)
                            ->leftJoin('TBLCASABIT', 'TBLCASABIT.CARI_KOD', '=', 'TBLSTHAR.STHAR_CARIKOD')
                            ->orderBy('STHAR_TARIH', 'desc')->get(array(DB::raw('dbo.TRK(TBLSTHAR.STOK_KODU) as STOK_KODU'), 'TBLSTHAR.FISNO', 'TBLSTHAR.STHAR_GCMIK', 'TBLSTHAR.STHAR_TARIH'
                            , DB::raw('dbo.TRK(TBLSTHAR.STHAR_CARIKOD) as STHAR_CARIKOD'), DB::raw('dbo.TRK(TBLCASABIT.CARI_ISIM) as CARI_ISIM'), 'TBLSTHAR.STHAR_SIPNUM'
                            , DB::raw('dbo.TRK(TBLSTHAR.STHAR_ACIKLAMA) as STHAR_ACIKLAMA'), 'TBLSTHAR.INCKEYNO'));
                        foreach ($bilgi[$i]['depokayitlari'] as $depokayidi) {
                            $depokayidi->STHAR_GCMIK = intval($depokayidi->STHAR_GCMIK);
                            $date = new DateTime($depokayidi->STHAR_TARIH);
                            $depokayidi->STHAR_TARIH = $date->format('d-m-Y');
                        }
                    }
                }
            }else{
                for ($i=0;$i<count($secilenler);$i++){
                    $inckeyno = $secilenler[$i];
                    $depokayit = Sthar::where('TBLSTHAR.INCKEYNO',$inckeyno)
                        ->orderBy('STHAR_TARIH','desc')->first(array(DB::raw('dbo.TRK(TBLSTHAR.STOK_KODU) as STOK_KODU'),'TBLSTHAR.FISNO','TBLSTHAR.STHAR_GCMIK',
                            'TBLSTHAR.STHAR_TARIH',DB::raw('dbo.TRK(TBLSTHAR.STHAR_CARIKOD) as STHAR_CARIKOD'),'TBLSTHAR.STHAR_SIPNUM'
                        ,DB::raw('dbo.TRK(TBLSTHAR.STHAR_ACIKLAMA) as STHAR_ACIKLAMA'),'TBLSTHAR.INCKEYNO'));
                    $depokayit->netsisstokkod=NetsisStokKod::where('kodu',$depokayit->STOK_KODU)->first();
                    $bilgi[$i]['kayit']=$depokayit;
                    $netsisstokkod = NetsisStokKod::where('kodu',$depokayit->STOK_KODU)->first();
                    $uretimurunler = UretimUrun::where('db_name',$dbname)->where('netsisstokkod_id',$netsisstokkod->id)->get(array('inckeyno'))->toArray();
                    $bilgi[$i]['depokayitlari']=Sthar::where('TBLSTHAR.STHAR_GCKOD','G')->where(function ($query) {
                        $query->whereIn('TBLSTHAR.STHAR_FTIRSIP',array(2,4))->orWhereNull('TBLSTHAR.STHAR_FTIRSIP');})->where('TBLSTHAR.DEPO_KODU',1)
                        ->where('TBLSTHAR.STOK_KODU',$depokayit->STOK_KODU)->whereNotIn('TBLSTHAR.INCKEYNO',$uretimurunler)
                        ->leftJoin('TBLCASABIT','TBLCASABIT.CARI_KOD','=','TBLSTHAR.STHAR_CARIKOD')
                        ->orderBy('STHAR_TARIH','desc')->get(array(DB::raw('dbo.TRK(TBLSTHAR.STOK_KODU) as STOK_KODU'),'TBLSTHAR.FISNO','TBLSTHAR.STHAR_GCMIK','TBLSTHAR.STHAR_TARIH'
                        ,DB::raw('dbo.TRK(TBLSTHAR.STHAR_CARIKOD) as STHAR_CARIKOD'),DB::raw('dbo.TRK(TBLCASABIT.CARI_ISIM) as CARI_ISIM'),'TBLSTHAR.STHAR_SIPNUM'
                        ,DB::raw('dbo.TRK(TBLSTHAR.STHAR_ACIKLAMA) as STHAR_ACIKLAMA'),'TBLSTHAR.INCKEYNO'));
                    foreach ($bilgi[$i]['depokayitlari'] as $depokayidi){
                        $depokayidi->STHAR_GCMIK = intval($depokayidi->STHAR_GCMIK);
                        $date = new DateTime($depokayidi->STHAR_TARIH);
                        $depokayidi->STHAR_TARIH =  $date->format('d-m-Y');
                    }
                    $bilgi[$i]['ureticiler']=UretimUretici::all();
                    $bilgi[$i]['markalar'] = UretimUrunMarka::all();
                }
            }
            return Response::json(array('durum'=>true, 'bilgi' => $bilgi,'netsisstokkodlari'=>$netsisstokkodlari,'eski'=>$durum));
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum'=>false,'title' => 'Ürün Kayıt Bilgilerinde Hata Var!', 'text' => str_replace("'","\'",$e->getMessage()), 'type'=>'warning'));
        }
    }

    public function getUreticiekle() {
        try {
            $ureticiadi = Input::get('uretici');
            $uretici = UretimUretici::where('ureticiadi',$ureticiadi)->first();
            DB::beginTransaction();
            $yeniuretici=null;
            if($uretici){
                return Response::json(array('durum' => false, 'title' => 'Doğrulama Hatası', 'text' => 'Bu Üretici Bilgisi Mevcut!', 'type' => 'warning'));
            }else{
                $uretici = new UretimUretici;
                $uretici->ureticiadi =$ureticiadi;
                $uretici->save();
                $yeniuretici=$uretici;
            }
            $ureticiler=UretimUretici::all();
            DB::commit();
            return Response::json(array('durum' => true, 'ureticiler' => $ureticiler,'yeniuretici'=>$yeniuretici, 'title' => 'Üretici Bilgisi Eklendi', 'text' => 'Üretici Bilgisi Başarıyla Eklendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Response::json(array('durum' =>false, 'title' => 'Üretici Bilgisi Eklenemedi', 'text' => 'Üretici Bilgisi Eklenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getMarkaekle() {
        try {
            $markaadi = Input::get('marka');
            $marka = UretimUrunMarka::where('markaadi',$markaadi)->first();
            DB::beginTransaction();
            $yenimarka=null;
            if($marka){
                return Response::json(array('durum' => false, 'title' => 'Doğrulama Hatası', 'text' => 'Bu Marka Bilgisi Mevcut!', 'type' => 'warning'));
            }else{
                $marka = new UretimUrunMarka;
                $marka->markaadi =$markaadi;
                $marka->save();
                $yenimarka=$marka;
            }
            $markalar=UretimUrunMarka::all();
            DB::commit();
            return Response::json(array('durum' => true, 'markalar' => $markalar,'yenimarka'=>$yenimarka, 'title' => 'Marka Bilgisi Eklendi', 'text' => 'Marka Bilgisi Başarıyla Eklendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Response::json(array('durum' =>false, 'title' => 'Marka Bilgisi Eklenemedi', 'text' => 'Marka Bilgisi Eklenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function postUrunkayitekle() {
        try {
            if(Input::has('muadil'))
                $rules = ['adet' => 'required', 'depokayidi' => 'required', 'stokkodu' => 'required', 'uretici' => 'required', 'marka' => 'required', 'uretimtarih' => 'required','muadilkodu'=>'required'];
            else
                $rules = ['adet' => 'required', 'depokayidi' => 'required', 'stokkodu' => 'required', 'uretici' => 'required', 'marka' => 'required', 'uretimtarih' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $count = Input::get('count');
            $stokkodlari = Input::get('stokkodu');
            $muadilkodlari = Input::get('muadilkodu');
            $depokayitlari = Input::get('depokayidi');
            $ureticiler = Input::get('uretici');
            $markalar = Input::get('marka');
            $adetler = Input::get('adet');
            $tarihler = Input::get('uretimtarih');
            $barkod1 = Input::get('barkod1');
            $barkod2 = Input::get('barkod2');
            $barkod3 = Input::get('barkod3');
            $barkodvar = false;
            If(Input::has('barkodvar')){
                $barkodvar = true;
            }
            $dbname = 'MANAS' . date('Y');
            if (count($depokayitlari) > 0) {
                $barkodlar=array();
                for ($i = 0; $i < $count; $i++) {
                    $inckeyno = $depokayitlari[$i];
                    $stokkodu=$stokkodlari[$i];
                    $muadilkodu=$muadilkodlari[$i];
                    $uretici = $ureticiler[$i];
                    $marka = $markalar[$i];
                    $adet = $adetler[$i];
                    $urunbarkod1 = $barkod1[$i];
                    $urunbarkod2 = $barkod2[$i];
                    $urunbarkod3 = $barkod3[$i];
                    $tarih=null;
                    $depokayidi = Sthar::where('INCKEYNO',$inckeyno)->first(array(DB::raw('dbo.TRK(STOK_KODU) as STOK_KODU'),'FISNO','STHAR_GCMIK','STHAR_TARIH',
                        DB::raw('dbo.TRK(STHAR_CARIKOD) as STHAR_CARIKOD'),'STHAR_SIPNUM',DB::raw('dbo.TRK(STHAR_ACIKLAMA) as STHAR_ACIKLAMA')));
                    if(!$depokayidi){
                        Input::flash();
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Depo Kayıdı Hatalı', 'text' => 'Depo Kayıdı Bulunamadı!', 'type' => 'error'));
                    }
                    if(isset($stokkodu)){
                        if($stokkodu==""){
                            Input::flash();
                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Stok Kodu Hatalı', 'text' => 'Stok Kodu Boş Geçilmiş!', 'type' => 'error'));
                        }
                    }
                    if(isset($uretici)){
                        if($uretici==""){
                            Input::flash();
                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Üretici Hatalı', 'text' => 'Üretici Bilgisi Boş Geçilmiş!', 'type' => 'error'));
                        }
                    }
                    if(isset($adet)){
                        if($adet==""){
                            Input::flash();
                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Miktar Hatalı', 'text' => 'Ürün Adet Bilgisi Boş Geçilmiş!', 'type' => 'error'));
                        }
                    }
                    if(isset($tarihler[$i])){
                        if($tarihler[$i]==""){
                            Input::flash();
                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Üretim Tarihi Hatalı', 'text' => 'Üretim Tarihi Bilgisi Boş Geçilmiş!', 'type' => 'error'));
                        }else{
                            $tarih = date("Y-m-d", strtotime($tarihler[$i]));
                        }
                    }
                    $netsisstokkod = NetsisStokKod::find($stokkodu);
                    $netsiscari = NetsisCari::where('carikod',$depokayidi->STHAR_CARIKOD)->first();

                    $uretimurun = new UretimUrun;
                    $uretimurun->urunadi = $netsisstokkod->adi;
                    $uretimurun->netsisstokkod_id = $stokkodu;
                    $uretimurun->muadil = $muadilkodu!="" ? $muadilkodu : null;
                    $uretimurun->netsiscari_id = $netsiscari ? $netsiscari->id : null;
                    $uretimurun->uretimuretici_id = $uretici;
                    $uretimurun->uretimurunmarka_id = $marka;
                    $uretimurun->uretimtarihi = $tarih;
                    $uretimurun->adet = $adet;
                    $uretimurun->kalan = $adet;
                    $uretimurun->kullanilan = 0;
                    $uretimurun->depotarihi = $depokayidi->STHAR_TARIH;
                    $uretimurun->db_name = $dbname;
                    $uretimurun->faturano = $depokayidi->FISNO;
                    $uretimurun->inckeyno = $inckeyno;
                    $uretimurun->birimfiyat = $depokayidi->STHAR_NF;
                    $uretimurun->aciklama = is_null($depokayidi->FISNO) ? $depokayidi->STHAR_ACIKLAMA : null;
                    $uretimurun->kullanici_id = Auth::user()->id;
                    $uretimurun->eklenmetarihi = date('Y-m-d H:i:s');
                    $uretimurun->guncellenmetarihi = date("Y-m-d H:i:s");
                    $uretimurun->urunbarkod1 = $urunbarkod1;
                    $uretimurun->urunbarkod2 = $urunbarkod2;
                    $uretimurun->urunbarkod3 = $urunbarkod3;
                    $uretimurun->barkod = BackendController::BarkodOlustur($uretimurun);
                    $uretimurun->save();
                    array_push($barkodlar,$uretimurun->barkod);
                }
                $eklenenler=implode(',',$depokayitlari);
                BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-thumbs-o-up', $count.' Adet Ürün Kayıdı Yapıldı.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Depo Kayıt Numaraları:' . $eklenenler);
                DB::commit();
                return Redirect::to($this->servisadi.'/urunkayit')->with(array('barkodvar'=>$barkodvar,'barkodlar'=>$barkodlar,'mesaj' => 'true', 'title' => 'Ürün Kayıdı Yapıldı', 'text' => 'Ürün Kayıdı Başarıyla Yapıldı', 'type' => 'success'));
            }else{
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Ürün Kayıdı Yapılamadı', 'text' => 'Girilen Ürünlerin Bilgilerinde Eksik Var', 'type' => 'error'));
            }
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            Input::flash();
            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Ürün Kayıdı Yapılamadı', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getUrunkayitsil($id){
        try {
            DB::beginTransaction();
            $uretimurun = UretimUrun::find($id);
            $uretimsonu = UretimSonu::where('uretimurun_id',$id)->first();
            if($uretimsonu){
                DB::rollBack();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ürün Kayıdı Silinemez', 'text' => 'Ürün İçin Üretim Sonu Kayıdı Var!', 'type' => 'error'));
            }
            $uretimurun->delete();
            DB::commit();
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ürün Kayıdı Silindi', 'text' => 'Ürün Kayıdı Başarıyla Silindi.', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ürün Kayıdı Silinemedi', 'text' => 'Ürün Kayıdı Silinirken Sorun Oluştu.', 'type' => 'error'));
        }
    }

    public function getUrunkayitduzenle($id) {
        $uretimurun = UretimUrun::find($id);
        $ureticiler = UretimUretici::all();
        $markalar = UretimUrunMarka::all();
        $netsisstokkodlari=NetsisStokKod::where('grupkodu','MALZEME')->get();
        return View::make($this->servisadi.'.urunkayitduzenle',array('uretimurun'=>$uretimurun,'ureticiler'=>$ureticiler,'markalar'=>$markalar,'netsisstokkodlari'=>$netsisstokkodlari))->with(array('title'=>$this->servisbilgi.' Ürün Kayıdı Düzenle'));
    }

    public function postUrunkayitduzenle($id) {
        try {
            if(Input::has('muadil'))
                $rules = ['adet' => 'required', 'depokayidi' => 'required', 'stokkodu' => 'required', 'uretici' => 'required', 'marka' => 'required', 'uretimtarih' => 'required','muadilkodu'=>'required'];
            else
                $rules = ['adet' => 'required', 'depokayidi' => 'required', 'stokkodu' => 'required', 'uretici' => 'required', 'marka' => 'required', 'uretimtarih' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $olduretimurun = UretimUrun::find($id);
            $bilgi = clone $olduretimurun;
            $count = Input::get('count');
            $stokkodlari = Input::get('stokkodu');
            $muadilkodlari = Input::get('muadilkodu');
            $depokayitlari = Input::get('depokayidi');
            $ureticiler = Input::get('uretici');
            $markalar = Input::get('marka');
            $adetler = Input::get('adet');
            $tarihler = Input::get('uretimtarih');
            $barkod1 = Input::get('barkod1');
            $barkod2 = Input::get('barkod2');
            $barkod3 = Input::get('barkod3');
            $barkodvar = false;
            If(Input::has('barkodvar')){
                $barkodvar = true;
            }
            $dbname = 'MANAS' . date('Y');
            if (count($depokayitlari) > 0) {
                $barkodlar=array();
                for ($i = 0; $i < $count; $i++) {
                    $inckeyno = $depokayitlari[$i];
                    $stokkodu=$stokkodlari[$i];
                    $muadilkodu=$muadilkodlari[$i];
                    $uretici = $ureticiler[$i];
                    $marka = $markalar[$i];
                    $adet = $adetler[$i];
                    $urunbarkod1 = $barkod1[$i];
                    $urunbarkod2 = $barkod2[$i];
                    $urunbarkod3 = $barkod3[$i];
                    $tarih=null;
                    $depokayidi = Sthar::where('INCKEYNO',$inckeyno)->first(array(DB::raw('dbo.TRK(STOK_KODU) as STOK_KODU'),'FISNO','STHAR_GCMIK','STHAR_TARIH',
                        DB::raw('dbo.TRK(STHAR_CARIKOD) as STHAR_CARIKOD'),'STHAR_SIPNUM',DB::raw('dbo.TRK(STHAR_ACIKLAMA) as STHAR_ACIKLAMA')));
                    if(!$depokayidi){
                        Input::flash();
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Depo Kayıdı Hatalı', 'text' => 'Depo Kayıdı Bulunamadı!', 'type' => 'error'));
                    }
                    if(isset($stokkodu)){
                        if($stokkodu==""){
                            Input::flash();
                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Stok Kodu Hatalı', 'text' => 'Stok Kodu Boş Geçilmiş!', 'type' => 'error'));
                        }
                    }
                    if(isset($uretici)){
                        if($uretici==""){
                            Input::flash();
                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Üretici Hatalı', 'text' => 'Üretici Bilgisi Boş Geçilmiş!', 'type' => 'error'));
                        }
                    }
                    if(isset($adet)){
                        if($adet==""){
                            Input::flash();
                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Miktar Hatalı', 'text' => 'Ürün Adet Bilgisi Boş Geçilmiş!', 'type' => 'error'));
                        }
                    }
                    if(isset($tarihler[$i])){
                        if($tarihler[$i]==""){
                            Input::flash();
                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Üretim Tarihi Hatalı', 'text' => 'Üretim Tarihi Bilgisi Boş Geçilmiş!', 'type' => 'error'));
                        }else{
                            $tarih = date("Y-m-d", strtotime($tarihler[$i]));
                        }
                    }
                    $netsisstokkod = NetsisStokKod::find($stokkodu);
                    $netsiscari = NetsisCari::where('carikod',$depokayidi->STHAR_CARIKOD)->first();

                    if($olduretimurun->inckeyno==$inckeyno){
                        if(($adet-$olduretimurun->kullanilan)<=0){
                            Input::flash();
                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Ürün Kayıdı Yapılamadı', 'text' => 'Bu Ürün için Belirlenen Miktar Kullanılan Miktardan Daha Az!', 'type' => 'error'));
                        }
                        $uretimurun = $olduretimurun;
                        $uretimurun->adet = $adet;
                        $uretimurun->kalan = $adet-$uretimurun->kullanilan;
                    }
                    else{
                        $uretimurun = new UretimUrun;
                        $uretimurun->adet = $adet;
                        $uretimurun->kalan = $adet;
                        $uretimurun->kullanilan = 0;
                    }
                    $uretimurun->urunadi = $netsisstokkod->adi;
                    $uretimurun->netsisstokkod_id = $stokkodu;
                    if($olduretimurun->muadil!=null && !BackendController::Muadilkontrol($uretimurun)) {
                        Input::flash();
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Ürün Kayıdı Yapılamadı', 'text' => 'Bu Ürün Muadil Olarak Kullanılmış. Muadil durumu değiştirilemez!', 'type' => 'error'));
                    }
                    $uretimurun->muadil = $muadilkodu!="" ? $muadilkodu : null;
                    $uretimurun->netsiscari_id = $netsiscari ? $netsiscari->id : null;
                    $uretimurun->uretimuretici_id = $uretici;
                    $uretimurun->uretimurunmarka_id = $marka;
                    $uretimurun->uretimtarihi = $tarih;
                    $uretimurun->depotarihi = $depokayidi->STHAR_TARIH;
                    $uretimurun->db_name = $dbname;
                    $uretimurun->faturano = $depokayidi->FISNO;
                    $uretimurun->inckeyno = $inckeyno;
                    $uretimurun->birimfiyat = $depokayidi->STHAR_NF;
                    $uretimurun->aciklama = is_null($depokayidi->FISNO) ? $depokayidi->STHAR_ACIKLAMA : null;
                    $uretimurun->kullanici_id = Auth::user()->id;
                    $uretimurun->eklenmetarihi = date('Y-m-d H:i:s');
                    $uretimurun->guncellenmetarihi = date("Y-m-d H:i:s");
                    $uretimurun->urunbarkod1 = $urunbarkod1;
                    $uretimurun->urunbarkod2 = $urunbarkod2;
                    $uretimurun->urunbarkod3 = $urunbarkod3;
                    $uretimurun->barkod = BackendController::BarkodOlustur($uretimurun,$bilgi);
                    $uretimurun->save();
                    array_push($barkodlar,$uretimurun->barkod);
                }
                $eklenenler=implode(',',$depokayitlari);
                BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-thumbs-o-up', $count.' Adet Ürün Kayıdı Yenilendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Depo Kayıt Numaraları:' . $eklenenler);
                DB::commit();
                return Redirect::to($this->servisadi.'/urunkayit')->with(array('barkodvar'=>$barkodvar,'barkodlar'=>$barkodlar,'mesaj' => 'true', 'title' => 'Ürün Kayıdı Yenilendi', 'text' => 'Ürün Kayıdı Başarıyla Yenilendi', 'type' => 'success'));
            }else{
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Ürün Kayıdı Yapılamadı', 'text' => 'Girilen Ürünlerin Bilgilerinde Eksik Var', 'type' => 'error'));
            }
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            Input::flash();
            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Ürün Kayıdı Yapılamadı', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getUrunbilgi(){
        try {
            $urunid=Input::get('id');
            $uretimurun=UretimUrun::find($urunid);
            $uretimurun->uretici=UretimUretici::find($uretimurun->uretimuretici_id);
            $uretimurun->marka=UretimUrunMarka::find($uretimurun->uretimurunmarka_id);
            $uretimurun->netsiscari=NetsisCari::find($uretimurun->netsiscari_id);
            $uretimurun->netsisstokkod=NetsisStokKod::find($uretimurun->netsisstokkod_id);
            return Response::json(array('durum'=>true, 'uretimurun' => $uretimurun));
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum'=>false,'title' => 'Ürün Bilgilerinde Hata Var!', 'text' => str_replace("'","\'",$e->getMessage()), 'type'=>'warning'));
        }
    }

    public function getUrunkayitgoster($id) {
        try {
            $uretimurun = UretimUrun::find($id);
            $uretimurun->uretici = UretimUretici::find($uretimurun->uretimuretici_id);
            $uretimurun->marka = UretimUrunMarka::find($uretimurun->uretimurunmarka_id);
            $uretimurun->netsisstokkod = NetsisStokKod::find($uretimurun->netsisstokkod_id);
            $uretimurun->muadilkodu = $uretimurun->muadil!=null ? NetsisStokKod::find($uretimurun->muadil) : "";
            $depokayidi = Sthar::where('INCKEYNO',$uretimurun->inckeyno)
                ->leftJoin('TBLCASABIT','TBLCASABIT.CARI_KOD','=','TBLSTHAR.STHAR_CARIKOD')
                ->first(array(DB::raw('dbo.TRK(TBLSTHAR.STOK_KODU) as STOK_KODU'),'TBLSTHAR.FISNO','TBLSTHAR.STHAR_GCMIK','TBLSTHAR.STHAR_TARIH',
                DB::raw('dbo.TRK(TBLSTHAR.STHAR_CARIKOD) as STHAR_CARIKOD'),'TBLSTHAR.STHAR_SIPNUM',
                    DB::raw('dbo.TRK(TBLSTHAR.STHAR_ACIKLAMA) as STHAR_ACIKLAMA'),DB::raw('dbo.TRK(TBLCASABIT.CARI_ISIM) as CARI_ISIM')));
            $depokayidi->STHAR_GCMIK = intval($depokayidi->STHAR_GCMIK);
            $date = new DateTime($depokayidi->STHAR_TARIH);
            $depokayidi->STHAR_TARIH =  $date->format('d-m-Y');
            if(($depokayidi->FISNO)==null){
                $uretimurun->depokayitbilgi = $depokayidi->STHAR_ACIKLAMA .' - '. $depokayidi->STHAR_TARIH .' Tarihli - '.
                    $depokayidi->STHAR_GCMIK . ' Adet ';
            }else{
                $uretimurun->depokayitbilgi = $depokayidi->FISNO .' - '. $depokayidi->STHAR_TARIH .' Tarihli - '.
                    $depokayidi->STHAR_GCMIK . ' Adet - ' . $depokayidi->STHAR_CARIKOD . ' - ' . $depokayidi->CARI_ISIM;
            }
            return View::make($this->servisadi.'.urunkayitgoster',array('uretimurun'=>$uretimurun))->with(array('title'=>$this->servisbilgi.' Ürün Kayıdı Bilgi Ekranı'));
        } catch (Exception $e) {
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ürün Kayıdı Bilgilerinde Hata Var!', 'text' => str_replace("'","'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getUretimsonukayit() {
        $agent = new Agent();
        $mobile = $agent->isMobile();
        return View::make($this->servisadi.'.uretimsonukayit')->with(array('mobile'=>$mobile))->with(array('title'=>$this->servisbilgi.' Sonu Kayıdı'));
    }

    public function postUretimsonukayitlist() {
        $query = UretimSonu::select(array("uretimsonu.id","netsisstokkod.adi","netsisstokkod.kodu","uretimsonu.isemrino","uretimsonu.adet",
            "uretimsonu.girisdepo","uretimsonu.eklenmetarihi","uretimsonu.geklenmetarihi","netsisstokkod.nadi",
            "netsisstokkod.nkodu","uretimsonu.kullanim"))
            ->leftjoin("netsisstokkod", "uretimsonu.netsisstokkod_id", "=", "netsisstokkod.id");

        return Datatables::of($query)
            ->editColumn('eklenmetarihi', function ($model) {
                $date = new DateTime($model->eklenmetarihi);
                return $date->format('d-m-Y');})
            ->addColumn('islemler',function ($model) {
                $root = BackendController::getRootDizin();
                if($model->kullanim==0)
                    return "<a class='btn btn-sm btn-warning' href='".$root."/".$this->servisadi."/uretimsonukayitduzenle/".$model->id."' > Düzenle </a>
                        <a href='#portlet-delete' data-toggle='modal' data-id='".$model->id."' class='btn btn-sm btn-danger delete' data-original-title='' title=''>Sil</a>";
                else
                    return "<a class='btn btn-sm btn-info' href='".$root."/".$this->servisadi."/uretimsonukayitgoster/".$model->id."' > Göster </a>";
            })
            ->make(true);
    }

    public function getUretimsonuurunbilgi(){
        try {
            $uretimsonuid=Input::get('id');
            $uretimsonu=UretimSonu::find($uretimsonuid);
            $uretimsonu->urun=UretimSonuUrun::where('uretimsonu_id',$uretimsonu->id)->get();
            $uretimsonu->netsisstokkod=NetsisStokKod::find($uretimsonu->netsisstokkod_id);
            return Response::json(array('durum'=>true, 'uretimsonu' => $uretimsonu));
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum'=>false,'title' => 'Üretim Sonu Kayıt Bilgisinde Hata Var!', 'text' => str_replace("'","\'",$e->getMessage()), 'type'=>'warning'));
        }
    }

    public function getUretimsonukayitekle() {
        try {
            $acikisemirleri = AcikIsEmri::orderBy('TARIH','desc')->get(array(DB::raw('dbo.TRK(ISEMRINO) as ISEMRINO'), 'TARIH', DB::raw('dbo.TRK(STOK_KODU) as STOK_KODU'), DB::raw('dbo.TRK(STOK_ADI) as STOK_ADI'),
                'MIKTAR', 'URETILENMIKTAR', 'KALANMIKTAR', DB::raw('dbo.TRK(ACIKLAMA) as ACIKLAMA'), 'TESLIM_TARIHI', 'SIPARIS_NO', 'FISNO',
                DB::raw('dbo.TRK(CARI_ISIM) as CARI_ISIM')));
            foreach ($acikisemirleri as $isemri) {
                $isemri->MIKTAR = intval($isemri->MIKTAR);
                $isemri->URETILENMIKTAR = intval($isemri->URETILENMIKTAR);
                $isemri->KALANMIKTAR = intval($isemri->KALANMIKTAR);
                $date = new DateTime($isemri->TARIH);
                $isemri->TARIH = $date->format('d-m-Y');
                $date = new DateTime($isemri->TESLIM_TARIHI);
                $isemri->TESLIM_TARIHI = $date->format('d-m-Y');
            }
            $netsisdepolar = NetsisDepolar::all();
            return View::make($this->servisadi . '.uretimsonukayitekle', array('acikisemirleri' => $acikisemirleri,'netsisdepolar'=>$netsisdepolar))->with(array('title' => $this->servisbilgi . ' Üretim Sonu Kayıdı Ekle'));
        } catch (Exception $e) {
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Üretim Sonu Kayıt Kısmında Hata Var!', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getUrunrecete(){
        try {
            $isemrino = Input::get('isemri');
            $kayitid = Input::get('kayit');
            if (Input::has('durum')) {
                $uretimsonukayit = UretimSonu::find($kayitid);
                if ($uretimsonukayit->db_name != 'MANAS' . date('Y')) { //eski kayit guncelleniyorsa
                    $connection = BackendController::AddNewConnection($uretimsonukayit->db_name);
                    $isemri = IsEmri::on($connection)->find(BackendController::ReverseTrk($isemrino));
                    if (!$isemri) {
                        BackendController::DeleteConnection($connection);
                        return Response::json(array('durum' => false, 'title' => 'Reçete Bilgilerinde Hata Var!', 'text' => 'İş Emri Bilgisi Bulunamadı!', 'type' => 'warning'));
                    }
                    $netsisstokkod = NetsisStokKod::where('kodu', $isemri->STOK_KODU)->first();
                    $isemri->MIKTAR = intval($isemri->MIKTAR);
                    $uretilen = StokUrs::on($connection)->where('URETSON_SIPNO', BackendController::ReverseTrk($isemrino))
                        ->where('URETSON_MAMUL', BackendController::ReverseTrk($netsisstokkod->kodu))->where('KAYIT_EDILSIN', 1)
                        ->groupBy('URETSON_SIPNO', 'URETSON_MAMUL')->first((array(DB::raw('SUM(URETSON_MIKTAR) AS URETILENMIKTAR'))));
                    if ($uretilen)
                        $isemri->URETILENMIKTAR = intval($uretilen->URETILENMIKTAR);
                    else
                        $isemri->URETILENMIKTAR = 0;
                    $isemri->KALANMIKTAR = $isemri->MIKTAR - $isemri->URETILENMIKTAR;
                    if (!is_null($isemri->SIPARIS_NO)) {
                        $sipatra = Sipatra::on($connection)->where('FISNO', $isemri->SIPARIS_NO)->first(DB::raw('dbo.TRK(TBLSIPATRA.STHAR_CARIKOD) as STHAR_CARIKOD'));
                        if ($sipatra) {
                            $netsiscari = NetsisCari::where('carikod', $sipatra->STHAR_CARIKOD)->first();
                            $isemri->CARI_ISIM = $netsiscari->cariadi;
                        }
                    }
                    $isemri->STOK_ADI = $netsisstokkod->adi;
                    $date = new DateTime($isemri->TARIH);
                    $isemri->TARIH = $date->format('d-m-Y');
                    $date = new DateTime($isemri->TESLIM_TARIHI);
                    $isemri->TESLIM_TARIHI = $date->format('d-m-Y');
                    $recete = BackendController::ReceteBilgisi($isemri->STOK_KODU);
                    if ($uretimsonukayit) {
                        foreach ($recete as $kalem) {
                            $kalem->netsisstokkod = NetsisStokKod::where('kodu', $kalem->HAM_KODU)->first();
                            $uretimurun = array();
                            if (count($kalem->muadiller) > 0) {
                                $uretimurun = UretimUrun::where('muadil', $kalem->netsisstokkod->id)->get(array('netsisstokkod_id'))->toArray();
                            }
                            array_push($uretimurun, array('netsisstokkod_id' => $kalem->netsisstokkod->id));
                            $kalem->uretimsonukullanilan = UretimSonuKullanilan::where('uretimsonu_id', $uretimsonukayit->id)->whereIn('netsisstokkod_id', $uretimurun)->get();
                        }
                        $uretilenseri = UretimSonuUrun::where('uretimsonu_id', '<>', $uretimsonukayit->id)->where('netsisstokkod_id', $netsisstokkod->id)->where('serino', 'LIKE', substr($netsisstokkod->kodu, 0, 1) . '%')->where('db_name', 'MANAS' . date('Y'))->orderBy('id', 'desc')->first();
                    } else {
                        BackendController::DeleteConnection($connection);
                        return Response::json(array('durum' => false, 'title' => 'Reçete Bilgilerinde Hata Var!', 'text' => 'Üretim Sonu Bilgisi Bulunamadı!', 'type' => 'warning'));
                    }
                    BackendController::DeleteConnection($connection);
                } else {
                    $isemri = IsEmri::find(BackendController::ReverseTrk($isemrino));
                    if (!$isemri) {
                        return Response::json(array('durum' => false, 'title' => 'Reçete Bilgilerinde Hata Var!', 'text' => 'İş Emri Bilgisi Bulunamadı!', 'type' => 'warning'));
                    }
                    $netsisstokkod = NetsisStokKod::where('kodu', $isemri->STOK_KODU)->first();
                    $isemri->MIKTAR = intval($isemri->MIKTAR);
                    $uretilen = StokUrs::where('URETSON_SIPNO', BackendController::ReverseTrk($isemrino))
                        ->where('URETSON_MAMUL', BackendController::ReverseTrk($netsisstokkod->kodu))->where('KAYIT_EDILSIN', 1)
                        ->groupBy('URETSON_SIPNO', 'URETSON_MAMUL')->first((array(DB::raw('SUM(URETSON_MIKTAR) AS URETILENMIKTAR'))));
                    if ($uretilen)
                        $isemri->URETILENMIKTAR = intval($uretilen->URETILENMIKTAR);
                    else
                        $isemri->URETILENMIKTAR = 0;
                    $isemri->KALANMIKTAR = $isemri->MIKTAR - $isemri->URETILENMIKTAR;
                    if (!is_null($isemri->SIPARIS_NO)) {
                        $sipatra = Sipatra::where('FISNO', $isemri->SIPARIS_NO)->first(DB::raw('dbo.TRK(TBLSIPATRA.STHAR_CARIKOD) as STHAR_CARIKOD'));
                        if ($sipatra) {
                            $netsiscari = NetsisCari::where('carikod', $sipatra->STHAR_CARIKOD)->first();
                            $isemri->CARI_ISIM = $netsiscari->cariadi;
                        }
                    }
                    $isemri->STOK_ADI = $netsisstokkod->adi;
                    $date = new DateTime($isemri->TARIH);
                    $isemri->TARIH = $date->format('d-m-Y');
                    $date = new DateTime($isemri->TESLIM_TARIHI);
                    $isemri->TESLIM_TARIHI = $date->format('d-m-Y');
                    $recete = BackendController::ReceteBilgisi($isemri->STOK_KODU);
                    if ($uretimsonukayit) {
                        foreach ($recete as $kalem) {
                            $kalem->netsisstokkod = NetsisStokKod::where('kodu', $kalem->HAM_KODU)->first();
                            $uretimurun = array();
                            if (count($kalem->muadiller) > 0) {
                                $uretimurun = UretimUrun::where('muadil', $kalem->netsisstokkod->id)->get(array('netsisstokkod_id'))->toArray();
                            }
                            array_push($uretimurun, array('netsisstokkod_id' => $kalem->netsisstokkod->id));
                            $kalem->uretimsonukullanilan = UretimSonuKullanilan::where('uretimsonu_id', $uretimsonukayit->id)->whereIn('netsisstokkod_id', $uretimurun)->get();
                        }
                        $uretilenseri = UretimSonuUrun::where('uretimsonu_id', '<>', $uretimsonukayit->id)->where('netsisstokkod_id', $netsisstokkod->id)->where('serino', 'LIKE', substr($netsisstokkod->kodu, 0, 1) . '%')->where('db_name', 'MANAS' . date('Y'))->orderBy('id', 'desc')->first();
                    } else {
                        return Response::json(array('durum' => false, 'title' => 'Reçete Bilgilerinde Hata Var!', 'text' => 'Üretim Sonu Bilgisi Bulunamadı!', 'type' => 'warning'));
                    }
                }
            }else{
                $isemri = IsEmri::find(BackendController::ReverseTrk($isemrino));
                if (!$isemri) {
                    return Response::json(array('durum' => false, 'title' => 'Reçete Bilgilerinde Hata Var!', 'text' => 'İş Emri Bilgisi Bulunamadı!', 'type' => 'warning'));
                }
                $netsisstokkod = NetsisStokKod::where('kodu', $isemri->STOK_KODU)->first();
                $isemri->MIKTAR = intval($isemri->MIKTAR);
                $uretilen = StokUrs::where('URETSON_SIPNO', BackendController::ReverseTrk($isemrino))
                    ->where('URETSON_MAMUL', BackendController::ReverseTrk($netsisstokkod->kodu))->where('KAYIT_EDILSIN', 1)
                    ->groupBy('URETSON_SIPNO', 'URETSON_MAMUL')->first((array(DB::raw('SUM(URETSON_MIKTAR) AS URETILENMIKTAR'))));
                if ($uretilen)
                    $isemri->URETILENMIKTAR = intval($uretilen->URETILENMIKTAR);
                else
                    $isemri->URETILENMIKTAR = 0;
                $isemri->KALANMIKTAR = $isemri->MIKTAR - $isemri->URETILENMIKTAR;
                if (!is_null($isemri->SIPARIS_NO)) {
                    $sipatra = Sipatra::where('FISNO', $isemri->SIPARIS_NO)->first(DB::raw('dbo.TRK(TBLSIPATRA.STHAR_CARIKOD) as STHAR_CARIKOD'));
                    if ($sipatra) {
                        $netsiscari = NetsisCari::where('carikod', $sipatra->STHAR_CARIKOD)->first();
                        $isemri->CARI_ISIM = $netsiscari->cariadi;
                    }
                }
                $isemri->STOK_ADI = $netsisstokkod->adi;
                $date = new DateTime($isemri->TARIH);
                $isemri->TARIH = $date->format('d-m-Y');
                $date = new DateTime($isemri->TESLIM_TARIHI);
                $isemri->TESLIM_TARIHI = $date->format('d-m-Y');
                $recete = BackendController::ReceteBilgisi($isemri->STOK_KODU);
                $uretilenseri = UretimSonuUrun::where('netsisstokkod_id', $netsisstokkod->id)->where('serino', 'LIKE', substr($netsisstokkod->kodu, 0, 1) . '%')->where('db_name', 'MANAS' . date('Y'))->orderBy('id', 'desc')->first();
            }
            if(!$uretilenseri){
                $uretilenserino = substr($netsisstokkod->kodu,0,1).date('y').substr($netsisstokkod->kodu,-3).'00001';
            }else{
                $uretilenserino = substr($uretilenseri->serino,0,6).BackendController::StringZeroAdd(substr($uretilenseri->serino,6)+1,5);
            }
            return Response::json(array('durum'=>true, 'isemri' => $isemri,'recete'=>$recete,'uretilenserino'=>$uretilenserino));
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum'=>false,'title' => 'Reçete Bilgilerinde Hata Var!', 'text' => str_replace("'","\'",$e->getMessage()), 'type'=>'warning'));
        }
    }

    public function postUretimsonukayitekle(){
        try {
            $rules = ['isemri' => 'required', 'cikisdepo' => 'required', 'girisdepo' => 'required', 'serino' => 'required', 'depokodu' => 'required', 'adet' => 'required', 'barkod' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $urunadet = Input::get('count');
            $isemrino = Input::get('isemri');
            $cikisdepo = Input::get('cikisdepo');
            $girisdepo = Input::get('girisdepo');
            $serinolar = Input::get('serino');
            $uretilecek = Input::get('uretilecek');
            $kalan = Input::get('kalan');
            $depokodlari = Input::get('depokodu');
            $stokkodlari = Input::get('stokkodu');
            $adetler = Input::get('adet');
            $barkodlar = Input::get('barkod');
            $dbname = 'MANAS' . date('Y');
            if($kalan<$uretilecek){
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Üretim Sonu Kayıdı Yapılamadı', 'text' => 'İş Emri İçin Kalan Miktar, Üretilecek Miktar için Yeterli Değil!', 'type' => 'error'));
            }
            if (intval($uretilecek) > 0) {
                $isemri = IsEmri::where('ISEMRINO',BackendController::ReverseTrk($isemrino))->first();
                if (!$isemri) {
                    Input::flash();
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'İş Emri Hatalı', 'text' => 'İş Emri Bulunamadı!', 'type' => 'error'));
                }
                $netsisstokkod = NetsisStokKod::where('kodu',$isemri->STOK_KODU)->first();

                $uretimsonukayit = new UretimSonu;
                $uretimsonukayit->db_name=$dbname;
                $uretimsonukayit->isemrino=$isemrino;
                $uretimsonukayit->netsisstokkod_id=$netsisstokkod->id;
                $uretimsonukayit->adet=$uretilecek;
                $uretimsonukayit->cikisdepo=$cikisdepo;
                $uretimsonukayit->girisdepo=$girisdepo;
                $uretimsonukayit->kullanici_id = Auth::user()->id;
                $uretimsonukayit->eklenmetarihi = date('Y-m-d H:i:s');
                $uretimsonukayit->guncellenmetarihi = date("Y-m-d H:i:s");
                $uretimsonukayit->save();

                foreach ($serinolar as $serino){
                    $uretimsonuurun = new UretimSonuUrun;
                    $uretimsonuurun->uretimsonu_id = $uretimsonukayit->id;
                    $uretimsonuurun->netsisstokkod_id = $netsisstokkod->id;
                    $uretimsonuurun->serino = $serino;
                    $uretimsonuurun->db_name = $dbname;
                    $uretimsonuurun->save();
                }

                for ($i = 0; $i < $urunadet; $i++) {
                    $depokodu = $depokodlari[$i];
                    $stokkodu = $stokkodlari[$i];
                    $adet = $adetler[$i];
                    if(!isset($barkodlar[$i])){
                        if($adet>0){
                            Input::flash();
                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Barkod Hatası', 'text' => 'Miktarı Sıfırdan Farklı Olan Ürün İçin Barkod Girilmemiş!', 'type' => 'error'));
                        }else{
                            continue;
                        }
                    }
                    $barkod = $barkodlar[$i];
                    if (isset($depokodu)) {
                        if ($depokodu == "") {
                            Input::flash();
                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Depo Kodu Hatalı', 'text' => 'Depo Kodu Boş Geçilmiş!', 'type' => 'error'));
                        }
                    }
                    $recete = BackendController::ReceteBilgisi($stokkodu);
                    if($recete->count()>0){ //alt ürünü varsa
                        if(count($barkod)>1){
                            $toplam = 0;
                            foreach ($barkod as $barkod_){
                                $urunnetsisstokkod = NetsisStokKod::where('kodu',$stokkodu)->first();
                                $uretimsonuurun = UretimSonuUrun::where('serino',$barkod_)->where('netsisstokkod_id',$urunnetsisstokkod->id)->first();
                                if(!$uretimsonuurun){
                                    Input::flash();
                                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Üretim Sonu Kaydı Hatalı', 'text' => 'Önce Alt Ürün için Üretim Sonu Kaydı Oluşturulmalı!', 'type' => 'error'));
                                }
                                $uretimsonukullanilan = UretimSonuKullanilan::where('uretimsonu_id',$uretimsonukayit->id)->where('netsisstokkod_id',$uretimsonuurun->netsisstokkod_id)->first();
                                if($uretimsonukullanilan){
                                    $uretimsonukullanilan->urunadet +=1;
                                }else{
                                    $uretimsonukullanilan = new UretimSonuKullanilan;
                                    $uretimsonukullanilan->uretimsonu_id = $uretimsonukayit->id;
                                    $uretimsonukullanilan->uretimurun_id = null;
                                    $uretimsonukullanilan->netsisstokkod_id = $uretimsonuurun->netsisstokkod_id;
                                    $uretimsonukullanilan->depokodu = $depokodu;
                                    $uretimsonukullanilan->db_name = $dbname;
                                    $uretimsonukullanilan->urunadet = 1;
                                    $anaurun = UretimSonuUrun::where('uretimsonu_id',$uretimsonukayit->id)->where('serino',$barkod_)->first();
                                    if(!$anaurun){
                                        Input::flash();
                                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Üretim Sonu Kaydı Hatalı', 'text' => 'Son Ürün Kaydedilmemiş!', 'type' => 'error'));
                                    }
                                    $uretimsonuurun->uretimsonuurun_id=$anaurun->id;
                                    $uretimsonuurun->save();
                                }
                                $uretimsonukullanilan->save();
                                $toplam +=1;
                            }
                            if($toplam<$adet){
                                DB::rollBack();
                                Input::flash();
                                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Üretim Sonu Kayıdı Yapılamadı', 'text' => $stokkodu.' Stok Koduna ait miktar üretim sonu için yeterli değil', 'type' => 'error'));
                            }
                        }else{
                            $urunnetsisstokkod = NetsisStokKod::where('kodu',$stokkodu)->first();
                            $uretimsonuurun = UretimSonuUrun::where('serino',$barkod[0])->where('netsisstokkod_id',$urunnetsisstokkod->id)->first();
                            if(!$uretimsonuurun){
                                Input::flash();
                                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Üretim Sonu Kaydı Hatalı', 'text' => 'Önce Alt Ürün için Üretim Sonu Kaydı Oluşturulmalı!', 'type' => 'error'));
                            }

                            $uretimsonukullanilan = UretimSonuKullanilan::where('uretimsonu_id',$uretimsonukayit->id)->where('netsisstokkod_id',$uretimsonuurun->netsisstokkod_id)->first();
                            if($uretimsonukullanilan){
                                $uretimsonukullanilan->urunadet +=1;
                            }else{
                                $uretimsonukullanilan = new UretimSonuKullanilan;
                                $uretimsonukullanilan->uretimsonu_id = $uretimsonukayit->id;
                                $uretimsonukullanilan->uretimurun_id = null;
                                $uretimsonukullanilan->netsisstokkod_id = $uretimsonuurun->netsisstokkod_id;
                                $uretimsonukullanilan->depokodu = $depokodu;
                                $uretimsonukullanilan->db_name = $dbname;
                                $uretimsonukullanilan->urunadet = 1;
                                $anaurun = UretimSonuUrun::where('uretimsonu_id',$uretimsonukayit->id)->where('serino',$barkod[0])->first();
                                if(!$anaurun){
                                    Input::flash();
                                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Üretim Sonu Kaydı Hatalı', 'text' => 'Son Ürün Kaydedilmemiş!', 'type' => 'error'));
                                }
                                $uretimsonuurun->uretimsonuurun_id=$anaurun->id;
                                $uretimsonuurun->save();
                            }
                            $uretimsonukullanilan->save();
                        }
                    }else{
                        if(count($barkod)>1){
                            $toplam = 0;
                            $kalan = $adet;
                            foreach ($barkod as $barkod_){
                                $uretimurun = UretimUrun::where('barkod',$barkod_)->first();
                                $uretimsonukullanilan = new UretimSonuKullanilan;
                                $uretimsonukullanilan->uretimsonu_id = $uretimsonukayit->id;
                                $uretimsonukullanilan->uretimurun_id = $uretimurun->id;
                                $uretimsonukullanilan->netsisstokkod_id = $uretimurun->netsisstokkod_id;
                                $uretimsonukullanilan->depokodu = $depokodu;
                                $uretimsonukullanilan->db_name = $dbname;
                                if($kalan>$uretimurun->kalan){
                                    $uretimsonukullanilan->urunadet = $uretimurun->kalan;
                                    $kalan-=$uretimurun->kalan;
                                    $toplam += $uretimurun->kalan;
                                    $uretimurun->kullanilan += $uretimurun->kalan;
                                    $uretimurun->kalan = 0;
                                    $uretimurun->save();
                                }else{
                                    $uretimsonukullanilan->urunadet = $kalan;
                                    $toplam += $kalan;
                                    $uretimurun->kullanilan += $kalan;
                                    $uretimurun->kalan -=$kalan;
                                    $uretimurun->save();
                                    $kalan=0;
                                }
                                $uretimsonukullanilan->save();
                                if($kalan==0)
                                    break;
                            }
                            if($toplam<$adet){
                                DB::rollBack();
                                Input::flash();
                                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Üretim Sonu Kayıdı Yapılamadı', 'text' => $stokkodu.' Stok Koduna ait miktar üretim sonu için yeterli değil', 'type' => 'error'));
                            }
                        }else{
                            $uretimurun = UretimUrun::where('barkod',$barkod[0])->first();
                            if($uretimurun->kalan<$adet){
                                DB::rollBack();
                                Input::flash();
                                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Üretim Sonu Kayıdı Yapılamadı', 'text' => $stokkodu.' Stok Koduna ait miktar üretim sonu için yeterli değil', 'type' => 'error'));
                            }
                            $uretimsonukullanilan = new UretimSonuKullanilan;
                            $uretimsonukullanilan->uretimsonu_id = $uretimsonukayit->id;
                            $uretimsonukullanilan->uretimurun_id = $uretimurun->id;
                            $uretimsonukullanilan->netsisstokkod_id = $uretimurun->netsisstokkod_id;
                            $uretimsonukullanilan->urunadet = $adet;
                            $uretimsonukullanilan->depokodu = $depokodu;
                            $uretimsonukullanilan->db_name = $dbname;
                            $uretimsonukullanilan->save();
                            $uretimurun->kullanilan += $adet;
                            $uretimurun->kalan -=$adet;
                            $uretimurun->save();
                        }
                    }
                }

                $kontrol = BackendController::UretimSonuKayitEkle($uretimsonukayit->id);
                if ($kontrol['durum'] == "1") {
                    BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-thumbs-o-up', $uretilecek.' Adet Üretim Sonu Kayıdı Yapıldı.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Kayıt Numarası:' . $uretimsonukayit->id);
                    DB::commit();
                    return Redirect::to($this->servisadi.'/uretimsonukayit')->with(array('mesaj' => 'true', 'title' => 'Üretim Sonu Kayıdı Yapıldı', 'text' => 'Üretim Sonu Kayıdı Başarıyla Yapıldı', 'type' => 'success'));
                } else {
                    $silmedurum = BackendController::UretimSonuKayitTemizle($uretimsonukayit->id,true);
                    DB::rollBack();
                    Input::flash();
                    if($silmedurum['durum']=="0"){
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Üretim Sonu Kayıdı Düzenlenemedi', 'text' => $silmedurum['text'], 'type' => 'error'));
                    }
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Üretim Sonu Kayıdı Yapılamadı', 'text' => $kontrol['text'], 'type' => 'error'));
                }
            }else{
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Üretim Sonu Kayıdı Yapılamadı', 'text' => 'Seri Numarası Girilmemiş!', 'type' => 'error'));
            }
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            Input::flash();
            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Üretim Sonu Kayıdı Yapılamadı', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getUretimsonukayitduzenle($id) {
        try {
            $uretimsonukayit = UretimSonu::find($id);
            $uretimsonukayit->netsisstokkod = NetsisStokKod::find($uretimsonukayit->netsisstokkod_id);
            $recete = BackendController::ReceteBilgisi($uretimsonukayit->netsisstokkod->kodu);
            $serinolar="";
            $uretimsonuurunler = UretimSonuUrun::where('uretimsonu_id',$uretimsonukayit->id)->get();
            foreach ($uretimsonuurunler as $uretimsonuurun){
                $serinolar .= ($serinolar==="" ? "" : ",").$uretimsonuurun->serino;
            }
            $uretimsonukayit->serinolar=$serinolar;
            $barkodlar="";
            foreach ($recete as $receteurun){
                $receteurun->netsisstokkod = NetsisStokKod::where('kodu',$receteurun->HAM_KODU)->first();
                $receteurun->uretimsonukullanilan = UretimSonuKullanilan::where('uretimsonu_id',$uretimsonukayit->id)->where('netsisstokkod_id',$receteurun->netsisstokkod->id)->get();
                $urunbarkodlar = "";
                $kullanilanadet = 0;
                foreach ($receteurun->uretimsonukullanilan as $kullanilan) {
                    $kullanilan->uretimurun = UretimUrun::find($kullanilan->uretimurun_id);
                    $barkodlar .= ($barkodlar === "" ? "" : ",") . $kullanilan->uretimurun->barkod;
                    $urunbarkodlar .= ($urunbarkodlar === "" ? "" : ",") . $kullanilan->uretimurun->barkod;
                    $kullanilanadet += $kullanilan->urunadet;
                }
                $receteurun->kullanilanadet = $kullanilanadet;
                $receteurun->secilenbarkodlar = $urunbarkodlar;
            }
            $uretimsonukayit->barkodlar=$barkodlar;
            if ($uretimsonukayit->db_name != 'MANAS' . date('Y')) { //eski kayit guncelleniyorsa
                $connection=BackendController::AddNewConnection($uretimsonukayit->db_name);
                $isemri = IsEmri::on($connection)->find(BackendController::ReverseTrk($uretimsonukayit->isemrino));
                if(!$isemri){
                    BackendController::DeleteConnection($connection);
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Üretim Sonu Kayıt Düzenleme Kısmında Hata Var!', 'text' =>'İş Emri Bilgisi Bulunamadı.', 'type' => 'error'));
                }
                $isemri->MIKTAR = intval($isemri->MIKTAR);
                $uretilen = StokUrs::where('URETSON_SIPNO',BackendController::ReverseTrk($uretimsonukayit->isemrino))
                    ->where('URETSON_MAMUL',BackendController::ReverseTrk($uretimsonukayit->netsisstokkod->kodu))->where('KAYIT_EDILSIN',1)
                    ->groupBy('URETSON_SIPNO','URETSON_MAMUL')->first((array(DB::raw('SUM(URETSON_MIKTAR) AS URETILENMIKTAR'))));
                if($uretilen)
                    $isemri->URETILENMIKTAR = intval($uretilen->URETILENMIKTAR);
                else
                    $isemri->URETILENMIKTAR = 0;
                $isemri->KALANMIKTAR = $isemri->MIKTAR - $isemri->URETILENMIKTAR;
                if(!is_null($isemri->SIPARIS_NO)){
                    $sipatra = Sipatra::where('FISNO',$isemri->SIPARIS_NO)->first(DB::raw('dbo.TRK(TBLSIPATRA.STHAR_CARIKOD) as STHAR_CARIKOD'));
                    if($sipatra){
                        $netsiscari = NetsisCari::where('carikod',$sipatra->STHAR_CARIKOD)->first();
                        $isemri->CARI_ISIM=$netsiscari->cariadi;
                    }
                }
                $netsisstokkod = NetsisStokKod::find($uretimsonukayit->netsisstokkod_id);
                $isemri->STOK_ADI = $netsisstokkod->adi;
                $date = new DateTime($isemri->TARIH);
                $isemri->TARIH = $date->format('d-m-Y');
                $date = new DateTime($isemri->TESLIM_TARIHI);
                $isemri->TESLIM_TARIHI = $date->format('d-m-Y');
                $netsisdepolar = NetsisDepolar::all();
                $uretilenseri = UretimSonuUrun::where('uretimsonu_id','<>',$id)->where('netsisstokkod_id',$netsisstokkod->id)->where('serino','LIKE',substr($netsisstokkod->kodu,0,1).'%')->where('db_name','MANAS'.date('Y'))->orderBy('id','desc')->first();
                if(!$uretilenseri){
                    $uretilenserino = substr($netsisstokkod->kodu,0,1).date('y').substr($netsisstokkod->kodu,-3).'00001';
                }else{
                    $uretilenserino = substr($uretilenseri->serino,0,6).BackendController::StringZeroAdd(substr($uretilenseri->serino,6)+1,5);
                }
                $uretimsonukayit->sonuretilen = $uretilenserino;
                BackendController::DeleteConnection($connection);
            }else{
                $isemri = IsEmri::where('ISEMRINO',BackendController::ReverseTrk($uretimsonukayit->isemrino))->first();
                if(!$isemri)
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Üretim Sonu Kayıt Düzenleme Kısmında Hata Var!', 'text' =>'İş Emri Bilgisi Bulunamadı.', 'type' => 'error'));
                $isemri->MIKTAR = intval($isemri->MIKTAR);
                $uretilen = StokUrs::where('URETSON_SIPNO',BackendController::ReverseTrk($uretimsonukayit->isemrino))
                    ->where('URETSON_MAMUL',BackendController::ReverseTrk($uretimsonukayit->netsisstokkod->kodu))->where('KAYIT_EDILSIN',1)
                    ->groupBy('URETSON_SIPNO','URETSON_MAMUL')->first((array(DB::raw('SUM(URETSON_MIKTAR) AS URETILENMIKTAR'))));
                if($uretilen)
                    $isemri->URETILENMIKTAR = intval($uretilen->URETILENMIKTAR);
                else
                    $isemri->URETILENMIKTAR = 0;
                $isemri->KALANMIKTAR = $isemri->MIKTAR - $isemri->URETILENMIKTAR;
                if(!is_null($isemri->SIPARIS_NO)){
                    $sipatra = Sipatra::where('FISNO',$isemri->SIPARIS_NO)->first(DB::raw('dbo.TRK(TBLSIPATRA.STHAR_CARIKOD) as STHAR_CARIKOD'));
                    if($sipatra){
                        $netsiscari = NetsisCari::where('carikod',$sipatra->STHAR_CARIKOD)->first();
                        $isemri->CARI_ISIM=$netsiscari->cariadi;
                    }
                }
                $netsisstokkod = NetsisStokKod::find($uretimsonukayit->netsisstokkod_id);
                $isemri->STOK_ADI = $netsisstokkod->adi;
                $date = new DateTime($isemri->TARIH);
                $isemri->TARIH = $date->format('d-m-Y');
                $date = new DateTime($isemri->TESLIM_TARIHI);
                $isemri->TESLIM_TARIHI = $date->format('d-m-Y');
                $netsisdepolar = NetsisDepolar::all();
                $uretilenseri = UretimSonuUrun::where('uretimsonu_id','<>',$id)->where('netsisstokkod_id',$netsisstokkod->id)->where('serino','LIKE',substr($netsisstokkod->kodu,0,1).'%')->where('db_name','MANAS'.date('Y'))->orderBy('id','desc')->first();
                if(!$uretilenseri){
                    $uretilenserino = substr($netsisstokkod->kodu,0,1).date('y').substr($netsisstokkod->kodu,-3).'00001';
                }else{
                    $uretilenserino = substr($uretilenseri->serino,0,6).BackendController::StringZeroAdd(substr($uretilenseri->serino,6)+1,5);
                }
                $uretimsonukayit->sonuretilen = $uretilenserino;
            }

            return View::make($this->servisadi . '.uretimsonukayitduzenle', array('uretimsonukayit' => $uretimsonukayit,'recete'=>$recete,'isemri'=>$isemri,'netsisdepolar'=>$netsisdepolar))->with(array('title' => $this->servisbilgi . ' Üretim Sonu Kayıdı Düzenle'));
        } catch (Exception $e) {
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Üretim Sonu Kayıt Düzenleme Kısmında Hata Var!', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function postUretimsonukayitduzenle($id){
        try {
            $rules = ['isemri' => 'required', 'cikisdepo' => 'required', 'girisdepo' => 'required', 'serino' => 'required', 'depokodu' => 'required', 'adet' => 'required', 'barkod' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $urunadet = Input::get('count');
            $isemrino = Input::get('isemri');
            $cikisdepo = Input::get('cikisdepo');
            $girisdepo = Input::get('girisdepo');
            $serinolar = Input::get('serino');
            $uretilecek = Input::get('uretilecek');
            $kalan = Input::get('kalan');
            $depokodlari = Input::get('depokodu');
            $stokkodlari = Input::get('stokkodu');
            $adetler = Input::get('adet');
            $barkodlar = Input::get('barkod');
            $dbname = 'MANAS' . date('Y');
            $uretimsonukayit = UretimSonu::find($id);
            $fark = $uretilecek-$uretimsonukayit->adet;
            if($kalan<$fark){
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Üretim Sonu Kayıdı Yapılamadı', 'text' => 'İş Emri İçin Kalan Miktar, Üretilecek Miktar için Yeterli Değil!', 'type' => 'error'));
            }
            if (intval($uretilecek) > 0) {
                $isemri = IsEmri::where('ISEMRINO',BackendController::ReverseTrk($isemrino))->first();
                if (!$isemri) {
                    Input::flash();
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'İş Emri Hatalı', 'text' => 'İş Emri Bulunamadı!', 'type' => 'error'));
                }
                $netsisstokkod = NetsisStokKod::where('kodu',$isemri->STOK_KODU)->first();
                $uretimsonukayit->db_name=$dbname;
                $uretimsonukayit->isemrino=$isemrino;
                $uretimsonukayit->netsisstokkod_id=$netsisstokkod->id;
                $uretimsonukayit->adet=$uretilecek;
                $uretimsonukayit->cikisdepo=$cikisdepo;
                $uretimsonukayit->girisdepo=$girisdepo;
                $uretimsonukayit->kullanici_id = Auth::user()->id;
                $uretimsonukayit->guncellenmetarihi = date("Y-m-d H:i:s");
                $uretimsonukayit->save();
                $eskiurunler = array();
                $eskiuretimsonuurunler = UretimSonuUrun::where('uretimsonu_id',$uretimsonukayit->id)->get();
                foreach ($eskiuretimsonuurunler as $urun){
                    array_push($eskiurunler,$urun->serino);
                }
                foreach ($serinolar as $serino){
                    $uretimsonuurun = UretimSonuUrun::where('uretimsonu_id',$uretimsonukayit->id)->where('serino',$serino)->first();
                    if(!$uretimsonuurun){
                        $uretimsonuurun = new UretimSonuUrun;
                    }
                    $uretimsonuurun->uretimsonu_id = $uretimsonukayit->id;
                    $uretimsonuurun->netsisstokkod_id = $netsisstokkod->id;
                    $uretimsonuurun->serino = $serino;
                    $uretimsonuurun->db_name = $dbname;
                    $uretimsonuurun->save();
                }
                $eskiurunler = array_diff($eskiurunler,$serinolar);
                foreach ($eskiurunler as $serino){
                   $eskiurun = UretimSonuUrun::where('uretimsonu_id',$uretimsonukayit->id)->where('serino',$serino)->first();
                   UretimSonuUrun::where('uretimsonuurun_id',$eskiurun->id)->update(['uretimsonuurun_id',null]);
                   $eskiurun->delete();
                }

                for ($i = 0; $i < $urunadet; $i++) {
                    $depokodu = $depokodlari[$i];
                    $stokkodu = $stokkodlari[$i];
                    $adet = $adetler[$i];
                    if(!isset($barkodlar[$i])){
                        if($adet>0){
                            Input::flash();
                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Barkod Hatası', 'text' => 'Miktarı Sıfırdan Farklı Olan Ürün İçin Barkod Girilmemiş!', 'type' => 'error'));
                        }else{
                            continue;
                        }
                    }
                    $barkod = $barkodlar[$i];
                    if (isset($depokodu)) {
                        if ($depokodu == "") {
                            Input::flash();
                            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Depo Kodu Hatalı', 'text' => 'Depo Kodu Boş Geçilmiş!', 'type' => 'error'));
                        }
                    }
                    $recete = BackendController::ReceteBilgisi($stokkodu);
                    if($recete->count()>0){ //alt ürünü varsa
                        if(count($barkod)>1){
                            $toplam = 0;
                            $status = false;
                            foreach ($barkod as $barkod_){
                                $urunnetsisstokkod = NetsisStokKod::where('kodu',$stokkodu)->first();
                                $uretimsonuurun = UretimSonuUrun::where('serino',$barkod_)->where('netsisstokkod_id',$urunnetsisstokkod->id)->first();
                                if(!$uretimsonuurun){
                                    Input::flash();
                                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Üretim Sonu Kaydı Hatalı', 'text' => 'Önce Alt Ürün için Üretim Sonu Kaydı Oluşturulmalı!', 'type' => 'error'));
                                }
                                $uretimsonukullanilan = UretimSonuKullanilan::where('uretimsonu_id',$uretimsonukayit->id)->where('netsisstokkod_id',$uretimsonuurun->netsisstokkod_id)->first();
                                if(!$status){
                                    if($uretimsonukullanilan){
                                        $uretimsonukullanilan->urunadet =count($barkod);
                                    }else{
                                        $uretimsonukullanilan = new UretimSonuKullanilan;
                                        $uretimsonukullanilan->uretimsonu_id = $uretimsonukayit->id;
                                        $uretimsonukullanilan->uretimurun_id = null;
                                        $uretimsonukullanilan->netsisstokkod_id = $uretimsonuurun->netsisstokkod_id;
                                        $uretimsonukullanilan->depokodu = $depokodu;
                                        $uretimsonukullanilan->db_name = $dbname;
                                        $uretimsonukullanilan->urunadet = count($barkod);
                                    }
                                    $uretimsonukullanilan->save();
                                    $status = true;
                                }
                                $anaurun = UretimSonuUrun::where('uretimsonu_id',$uretimsonukayit->id)->where('serino',$barkod_)->first();
                                if(!$anaurun){
                                    Input::flash();
                                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Üretim Sonu Kaydı Hatalı', 'text' => 'Son Ürün Kaydedilmemiş!', 'type' => 'error'));
                                }
                                $uretimsonuurun->uretimsonuurun_id=$anaurun->id;
                                $uretimsonuurun->save();
                                $toplam +=1;
                            }
                            if($toplam<$adet){
                                DB::rollBack();
                                Input::flash();
                                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Üretim Sonu Kayıdı Yapılamadı', 'text' => $stokkodu.' Stok Koduna ait miktar üretim sonu için yeterli değil', 'type' => 'error'));
                            }
                        }else{
                            $urunnetsisstokkod = NetsisStokKod::where('kodu',$stokkodu)->first();
                            $uretimsonuurun = UretimSonuUrun::where('serino',$barkod[0])->where('netsisstokkod_id',$urunnetsisstokkod->id)->first();
                            if(!$uretimsonuurun){
                                Input::flash();
                                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Üretim Sonu Kaydı Hatalı', 'text' => 'Önce Alt Ürün için Üretim Sonu Kaydı Oluşturulmalı!', 'type' => 'error'));
                            }

                            $uretimsonukullanilan = UretimSonuKullanilan::where('uretimsonu_id',$uretimsonukayit->id)->where('netsisstokkod_id',$uretimsonuurun->netsisstokkod_id)->first();
                            if($uretimsonukullanilan){
                                $uretimsonukullanilan->urunadet =1;
                            }else{
                                $uretimsonukullanilan = new UretimSonuKullanilan;
                                $uretimsonukullanilan->uretimsonu_id = $uretimsonukayit->id;
                                $uretimsonukullanilan->uretimurun_id = null;
                                $uretimsonukullanilan->netsisstokkod_id = $uretimsonuurun->netsisstokkod_id;
                                $uretimsonukullanilan->depokodu = $depokodu;
                                $uretimsonukullanilan->db_name = $dbname;
                                $uretimsonukullanilan->urunadet = 1;
                            }
                            $anaurun = UretimSonuUrun::where('uretimsonu_id',$uretimsonukayit->id)->where('serino',$barkod[0])->first();
                            if(!$anaurun){
                                Input::flash();
                                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Üretim Sonu Kaydı Hatalı', 'text' => 'Son Ürün Kaydedilmemiş!', 'type' => 'error'));
                            }
                            $uretimsonuurun->uretimsonuurun_id=$anaurun->id;
                            $uretimsonuurun->save();
                            $uretimsonukullanilan->save();
                        }
                    }else{
                        if(count($barkod)>1){
                            $toplam = 0;
                            $kalan = $adet;
                            foreach ($barkod as $barkod_){
                                $uretimurun = UretimUrun::where('barkod',$barkod_)->first();
                                $uretimsonukullanilan = UretimSonuKullanilan::where('uretimsonu_id', $uretimsonukayit->id)->where('uretimurun_id', $uretimurun->id)->first();
                                $bilgikullanilan = clone $uretimsonukullanilan;
                                if (!$uretimsonukullanilan) {
                                    $uretimsonukullanilan = new UretimSonuKullanilan;
                                    $uretimsonukullanilan->uretimsonu_id = $uretimsonukayit->id;
                                    $uretimsonukullanilan->uretimurun_id = $uretimurun->id;
                                    $uretimsonukullanilan->netsisstokkod_id = $uretimurun->netsisstokkod_id;
                                    $uretimsonukullanilan->depokodu = $depokodu;
                                    $uretimsonukullanilan->db_name = $dbname;
                                    if($kalan>$uretimurun->kalan){
                                        $uretimsonukullanilan->urunadet = $uretimurun->kalan;
                                        $kalan -= $uretimurun->kalan;
                                        $toplam += $uretimurun->kalan;
                                        $uretimurun->kullanilan += $uretimurun->kalan;
                                        $uretimurun->kalan = 0;
                                        $uretimurun->save();
                                    }else{
                                        $uretimsonukullanilan->urunadet = $kalan;
                                        $toplam += $kalan;
                                        $uretimurun->kullanilan += $kalan;
                                        $uretimurun->kalan -= $kalan;
                                        $uretimurun->save();
                                        $kalan = 0;
                                    }
                                } else {
                                    $uretimsonukullanilan->uretimsonu_id = $uretimsonukayit->id;
                                    $uretimsonukullanilan->uretimurun_id = $uretimurun->id;
                                    $uretimsonukullanilan->netsisstokkod_id = $uretimurun->netsisstokkod_id;
                                    $uretimsonukullanilan->depokodu = $depokodu;
                                    $uretimsonukullanilan->db_name = $dbname;
                                    if ($kalan > ($uretimurun->kalan + $bilgikullanilan->urunadet)) {
                                        $uretimsonukullanilan->urunadet = $uretimurun->kalan + $bilgikullanilan->urunadet;
                                        $kalan -= ($uretimurun->kalan + $bilgikullanilan->urunadet);
                                        $toplam += ($uretimurun->kalan + $bilgikullanilan->urunadet);
                                        $uretimurun->kullanilan += $uretimurun->kalan;
                                        $uretimurun->kalan = 0;
                                        $uretimurun->save();
                                    } else {
                                        $uretimsonukullanilan->urunadet = $kalan;
                                        $toplam += $kalan;
                                        $uretimurun->kullanilan += ($kalan - $bilgikullanilan->urunadet);
                                        $uretimurun->kalan -= ($kalan - $bilgikullanilan->urunadet);
                                        $uretimurun->save();
                                        $kalan = 0;
                                    }
                                }
                                $uretimsonukullanilan->save();
                                if($kalan == 0)
                                    break;
                            }
                            if($toplam<$adet){
                                DB::rollBack();
                                Input::flash();
                                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Üretim Sonu Kayıdı Yapılamadı', 'text' => $stokkodu . ' Stok Koduna ait miktar üretim sonu için yeterli değil', 'type' => 'error'));
                            }
                        }else{
                            $uretimurun = UretimUrun::where('barkod',$barkod[0])->first();
                            if($uretimurun->kalan<$adet){
                                DB::rollBack();
                                Input::flash();
                                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Üretim Sonu Kayıdı Yapılamadı', 'text' => $stokkodu . ' Stok Koduna ait miktar üretim sonu için yeterli değil', 'type' => 'error'));
                            }
                            $uretimsonukullanilan = UretimSonuKullanilan::where('uretimsonu_id', $uretimsonukayit->id)->where('uretimurun_id', $uretimurun->id)->first();
                            $bilgikullanilan = clone $uretimsonukullanilan;
                            if (!$uretimsonukullanilan) {
                                $uretimsonukullanilan = new UretimSonuKullanilan;
                                $uretimsonukullanilan->uretimsonu_id = $uretimsonukayit->id;
                                $uretimsonukullanilan->uretimurun_id = $uretimurun->id;
                                $uretimsonukullanilan->netsisstokkod_id = $uretimurun->netsisstokkod_id;
                                $uretimsonukullanilan->urunadet = $adet;
                                $uretimsonukullanilan->depokodu = $depokodu;
                                $uretimsonukullanilan->db_name = $dbname;
                                $uretimsonukullanilan->save();
                                $uretimurun->kullanilan += $adet;
                                $uretimurun->kalan -= $adet;
                            } else {
                                $uretimsonukullanilan->uretimsonu_id = $uretimsonukayit->id;
                                $uretimsonukullanilan->uretimurun_id = $uretimurun->id;
                                $uretimsonukullanilan->netsisstokkod_id = $uretimurun->netsisstokkod_id;
                                $uretimsonukullanilan->urunadet = $adet;
                                $uretimsonukullanilan->depokodu = $depokodu;
                                $uretimsonukullanilan->db_name = $dbname;
                                $uretimsonukullanilan->save();
                                $uretimurun->kullanilan += ($adet - $bilgikullanilan->urunadet);
                                $uretimurun->kalan -= ($adet - $bilgikullanilan->urunadet);
                            }
                            $uretimurun->save();
                        }
                    }
                }
                $kontrol = BackendController::UretimSonuKayitDuzenle($uretimsonukayit->id);
                if ($kontrol['durum'] == "1") {
                    BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-thumbs-o-up', $uretilecek.' Adet Üretim Sonu Kayıdı Düzenlendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Kayıt Numarası:' . $uretimsonukayit->id);
                    DB::commit();
                    return Redirect::to($this->servisadi.'/uretimsonukayit')->with(array('mesaj' => 'true', 'title' => 'Üretim Sonu Kayıdı Düzenlendi', 'text' => 'Üretim Sonu Kayıdı Başarıyla Düzenlendi', 'type' => 'success'));
                } else {
                    DB::rollBack();
                    Input::flash();
                    $silmedurum = BackendController::UretimSonuKayitTemizle($id);
                    if($silmedurum['durum']=="0"){
                        Input::flash();
                        return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Üretim Sonu Kayıdı Düzenlenemedi', 'text' => 'Netsis Tarafına Yazılan Bilgiler Silinemedi', 'type' => 'error'));
                    }
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Üretim Sonu Kayıdı Düzenlenemedi', 'text' => $kontrol['text'], 'type' => 'error'));
                }
            }else{
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Üretim Sonu Kayıdı Düzenlenemedi', 'text' => 'Seri Numarası Girilmemiş!', 'type' => 'error'));
            }
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            Input::flash();
            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Üretim Sonu Kayıdı Düzenlenemedi', 'text' => str_replace("'","\'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getUretimsonukayitgoster($id) {
        try {
            $uretimsonukayit = UretimSonu::find($id);
            $uretimsonukayit->netsisstokkod = NetsisStokKod::find($uretimsonukayit->netsisstokkod_id);
            $uretimsonukayit->girisdepobilgi = NetsisDepolar::where('kodu',$uretimsonukayit->girisdepo)->first();
            $uretimsonukayit->cikisdepobilgi = NetsisDepolar::where('kodu',$uretimsonukayit->cikisdepo)->first();
            $recete = BackendController::ReceteBilgisi($uretimsonukayit->netsisstokkod->kodu);
            $serinolar="";
            $uretimsonuurunler = UretimSonuUrun::where('uretimsonu_id',$uretimsonukayit->id)->get();
            foreach ($uretimsonuurunler as $uretimsonuurun){
                $serinolar .= ($serinolar==="" ? "" : ",").$uretimsonuurun->serino;
            }
            $uretimsonukayit->serinolar=$serinolar;
            $barkodlar="";
            foreach ($recete as $receteurun){
                $receteurun->netsisstokkod = NetsisStokKod::where('kodu',$receteurun->HAM_KODU)->first();
                $uretimurun=array();
                if(count($receteurun->muadiller)>0){
                    $uretimurun = UretimUrun::where('muadil',$receteurun->netsisstokkod->id)->get(array('netsisstokkod_id'))->toArray();
                }
                array_push($uretimurun,array('netsisstokkod_id'=>$receteurun->netsisstokkod->id));
                $receteurun->uretimsonukullanilan = UretimSonuKullanilan::where('uretimsonu_id',$uretimsonukayit->id)->whereIn('netsisstokkod_id',$uretimurun)->get();
                $urunbarkodlar = "";
                $kullanilanadet = 0;
                foreach ($receteurun->uretimsonukullanilan as $kullanilan) {
                    $kullanilan->uretimurun = UretimUrun::find($kullanilan->uretimurun_id);
                    $barkodlar .= ($barkodlar === "" ? "" : ",") . $kullanilan->uretimurun->barkod;
                    $urunbarkodlar .= ($urunbarkodlar === "" ? "" : ",") . $kullanilan->uretimurun->barkod;
                    $kullanilanadet += $kullanilan->urunadet;
                }
                $receteurun->kullanilanadet = $kullanilanadet;
                $receteurun->secilenbarkodlar = $urunbarkodlar;
            }
            $uretimsonukayit->barkodlar=$barkodlar;
            $isemri = IsEmri::where('ISEMRINO',BackendController::ReverseTrk($uretimsonukayit->isemrino))->first();
            $isemri->MIKTAR = intval($isemri->MIKTAR);
            $uretilen = StokUrs::where('URETSON_SIPNO',BackendController::ReverseTrk($uretimsonukayit->isemrino))
                ->where('URETSON_MAMUL',BackendController::ReverseTrk($uretimsonukayit->netsisstokkod->kodu))->where('KAYIT_EDILSIN',1)
                ->groupBy('URETSON_SIPNO','URETSON_MAMUL')->first((array(DB::raw('SUM(URETSON_MIKTAR) AS URETILENMIKTAR'))));
            if($uretilen)
                $isemri->URETILENMIKTAR = intval($uretilen->URETILENMIKTAR);
            else
                $isemri->URETILENMIKTAR = 0;
            $isemri->KALANMIKTAR = $isemri->MIKTAR - $isemri->URETILENMIKTAR;
            $netsisstokkod = NetsisStokKod::find($uretimsonukayit->netsisstokkod_id);
            if(!is_null($isemri->SIPARIS_NO)){
                $sipatra = Sipatra::where('FISNO',$isemri->SIPARIS_NO)->first(DB::raw('dbo.TRK(TBLSIPATRA.STHAR_CARIKOD) as STHAR_CARIKOD'));
                if($sipatra){
                    $netsiscari = NetsisCari::where('carikod',$sipatra->STHAR_CARIKOD)->first();
                    $isemri->CARI_ISIM=$netsiscari->cariadi;
                }
            }
            $isemri->STOK_ADI = $netsisstokkod->adi;
            $date = new DateTime($isemri->TARIH);
            $isemri->TARIH = $date->format('d-m-Y');
            $date = new DateTime($isemri->TESLIM_TARIHI);
            $isemri->TESLIM_TARIHI = $date->format('d-m-Y');
            return View::make($this->servisadi.'.uretimsonukayitgoster',array('uretimsonukayit'=>$uretimsonukayit,'isemri'=>$isemri,'recete'=>$recete))->with(array('title'=>$this->servisbilgi.' Üretim Sonu Kayıdı Bilgi Ekranı'));
        } catch (Exception $e) {
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ürün Kayıdı Bilgilerinde Hata Var!', 'text' => str_replace("'","'",$e->getMessage()), 'type' => 'error'));
        }
    }

    public function getUretimsonukayitsil($id){
        try {
            DB::beginTransaction();
            $uretimsonukayit = UretimSonu::find($id);
            $silmedurum = BackendController::UretimSonuKayitTemizle($uretimsonukayit->id,true);
            if($silmedurum['durum']=="0"){
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Üretim Sonu Kayıdı Silinemedi', 'text' => $silmedurum['text'], 'type' => 'error'));
            }

            $netsisstokkod = NetsisStokKod::find($uretimsonukayit->netsisstokkod_id);
            $recete = BackendController::ReceteUrunBilgisi($netsisstokkod->kodu);
            foreach ($recete as $urun) {
                $urun->netsisstokkod = NetsisStokKod::where('kodu',$urun->MAMUL_KODU)->first();
                $urun->uretimsonuurunler = UretimSonuUrun::where('netsisstokkod_id',$urun->netsisstokkod->id)
                    ->whereIn('serino',function ($query) use ($uretimsonukayit) {$query->select('serino')->from(with(new UretimSonuUrun)->getTable())
                        ->where('uretimsonu_id',$uretimsonukayit->id);})->first();
                if($urun->uretimsonuurunler){
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Üretim Sonu Kayıdı Silinemedi', 'text' => 'Oluşan Ürün Başka Üretim Sonu Kayıdında Kullanılmış!', 'type' => 'error'));
                }
            }
            UretimSonuUrun::where('uretimsonu_id',$uretimsonukayit->id)->delete();
            $uretimsonukullanilanlar=UretimSonuKullanilan::where('uretimsonu_id',$uretimsonukayit->id)->get();
            foreach ($uretimsonukullanilanlar as $kullanilan){
                $uretimurun = UretimUrun::find($kullanilan->uretimurun_id);
                $uretimurun->kullanilan -= $kullanilan->urunadet;
                $uretimurun->kalan += $kullanilan->urunadet;
                $uretimurun->save();
                $kullanilan->delete();
            }
            $uretimsonukayit->delete();
            DB::commit();
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Üretim Sonu Kayıdı Silindi', 'text' => 'Üretim Sonu Kayıdı Başarıyla Silindi.', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Üretim Sonu Kayıdı Silinemedi', 'text' => 'Üretim Sonu Kayıdı Silinirken Sorun Oluştu.', 'type' => 'error'));
        }
    }

    public function getRecetestokkontrol(){

        $stokkodlari = Input::get('stokkodlari');
        $depokodlari = Input::get('depokodlari');
        $adetler = Input::get('adetler');
        $eksikkodlar = array();
        $eksikadetler = array();
        if(Input::has('durum')){
            $id = Input::get('id');
            for($i=0;$i<count($stokkodlari);$i++) {
                if($depokodlari!=""){
                    $stokbakiye = StokBakiye::where('STOK_KODU', BackendController::ReverseTrk($stokkodlari[$i]))->where('DEPO_KODU', $depokodlari[$i])->first();
                    $netsisstokkod = NetsisStokKod::where('kodu',$stokkodlari[$i])->first();
                    $eskikullanilan = UretimSonuKullanilan::where('netsisstokkod_id',$netsisstokkod->id)->where('uretimsonu_id',$id)->first();
                    if (!$stokbakiye) {
                        if($eskikullanilan){
                            $kalan = $adetler[$i]-$eskikullanilan->urunadet;
                            if($kalan>0){
                                array_push($eksikkodlar,$stokkodlari[$i]);
                                array_push($eksikadetler,$kalan);
                            }
                        }else{
                            if($adetler[$i]>0){
                                array_push($eksikkodlar,$stokkodlari[$i]);
                                array_push($eksikadetler,$adetler[$i]);
                            }
                        }
                    }else{
                        if($eskikullanilan){
                            $kalan = $adetler[$i]-$eskikullanilan->urunadet;
                            if($stokbakiye->BAKIYE<$kalan){
                                $eksik = $kalan-$stokbakiye->BAKIYE;
                                array_push($eksikkodlar,$stokkodlari[$i]);
                                array_push($eksikadetler,$eksik);
                            }
                        }else{
                            if($stokbakiye->BAKIYE<$adetler[$i]){
                                $eksik = $adetler[$i]-$stokbakiye->BAKIYE;
                                array_push($eksikkodlar,$stokkodlari[$i]);
                                array_push($eksikadetler,$eksik);
                            }
                        }
                    }
                }else{
                    array_push($eksikkodlar,$stokkodlari[$i]);
                    array_push($eksikadetler,$adetler[$i]);
                }
            }
        }else{
            for($i=0;$i<count($stokkodlari);$i++) {
                if($depokodlari!=""){
                    $stokbakiye = StokBakiye::where('STOK_KODU', BackendController::ReverseTrk($stokkodlari[$i]))->where('DEPO_KODU', $depokodlari[$i])->first();
                    if (!$stokbakiye) {
                        if($adetler[$i]>0){
                            array_push($eksikkodlar,$stokkodlari[$i]);
                            array_push($eksikadetler,$adetler[$i]);
                        }
                    }else{
                        if($stokbakiye->BAKIYE<$adetler[$i]){
                            $eksik = $adetler[$i]-$stokbakiye->BAKIYE;
                            array_push($eksikkodlar,$stokkodlari[$i]);
                            array_push($eksikadetler,$eksik);
                        }
                    }
                }else{
                    array_push($eksikkodlar,$stokkodlari[$i]);
                    array_push($eksikadetler,$adetler[$i]);
                }
            }
        }
        if(count($eksikkodlar)>0)
            return Response::json(array('eksikkodlar'=>$eksikkodlar,'eksikadetler'=>$eksikadetler,'durum'=>false,'title' => 'Eksi Bakiye Mevcut!', 'text' => 'Reçeteye ait Kalemlerin Bazılarında Bakiye Eksik yada Mevcut Değil!', 'type'=>'warning'));
        else
            return Response::json(array('durum'=>true));

    }

    public function getUrunsorgulama() {
        return View::make($this->servisadi.'.urunsorgulama')->with(array('title'=>$this->servisbilgi.' Ürün - Parca Sorgulaam Ekranı'));
    }

    public function getUrunsorgulamabilgi() {
        try {
            $tip = Input::get('tip');
            $kriter = Input::get('kriter');
            switch ($tip) {
                case 1: // ürün
                    $urunbilgi = UretimSonuUrun::where('uretimsonuurun.serino', 'LIKE', '%' . $kriter . '%')
                        ->select(array("uretimsonuurun.id",DB::raw('1 as tipi'), "uretimsonu.id as uretimsonu_id", "uretimsonuurun.serino", "netsisstokkod.kodu",
                            "netsisstokkod.adi as stokadi","uretimsonuurun.netsisstokkod_id","uretimsonuurun.db_name","uretimsonuurun.sirano","uretimsonuurun.inckeyno",
                            "uretimsonuurun.birimfiyat","uretimsonu.isemrino","uretimsonu.cikisdepo","uretimsonu.girisdepo","uretimsonu.geklenmetarihi",
                            "uretimsonuurun.uretimsonuurun_id"))
                        ->leftjoin("uretimsonu", "uretimsonuurun.uretimsonu_id", "=", "uretimsonu.id")
                        ->leftjoin("netsisstokkod", "uretimsonuurun.netsisstokkod_id", "=", "netsisstokkod.id")->get();
                    break;
                case 2: // parça
                    $urunbilgi = UretimUrun::where('uretimurun.barkod', 'LIKE', '%' . $kriter . '%')
                        ->select(array("uretimurun.id",DB::raw('2 as tipi'), "uretimurun.urunadi as stokadi", "uretimurun.barkod", "netsisstokkod.kodu",
                            "uretimurun.netsisstokkod_id","uretimuretici.ureticiadi","uretimurunmarka.markaadi","uretimurun.guretimtarihi","uretimurun.adet","uretimurun.kullanilan",
                            "uretimurun.kalan","uretimurun.gdepotarihi","uretimurun.db_name","uretimurun.faturano","uretimurun.birimfiyat","uretimurun.inckeyno",
                            "uretimurun.aciklama","uretimurun.geklenmetarihi","uretimurun.urunbarkod1","uretimurun.urunbarkod2","uretimurun.urunbarkod3"))
                        ->leftjoin("netsisstokkod", "uretimurun.netsisstokkod_id", "=", "netsisstokkod.id")
                        ->leftjoin("uretimuretici", "uretimurun.uretimuretici_id", "=", "uretimuretici.id")
                        ->leftjoin("uretimurunmarka", "uretimurun.uretimurunmarka_id", "=", "uretimurunmarka.id")->get();
                    break;
                default: // ürün
                    $urunbilgi = UretimSonuUrun::where('uretimsonuurun.serino', 'LIKE', '%' . $kriter . '%')
                        ->select(array("uretimsonuurun.id",DB::raw('1 as tipi'), "uretimsonu.id as uretimsonu_id", "uretimsonuurun.serino", "netsisstokkod.kodu",
                            "netsisstokkod.adi as stokadi","uretimsonuurun.netsisstokkod_id","uretimsonuurun.db_name","uretimsonuurun.sirano","uretimsonuurun.inckeyno",
                            "uretimsonuurun.birimfiyat","uretimsonu.isemrino","uretimsonu.cikisdepo","uretimsonu.girisdepo","uretimsonu.geklenmetarihi",
                            "uretimsonuurun.uretimsonuurun_id"))
                        ->leftjoin("uretimsonu", "uretimsonuurun.uretimsonu_id", "=", "uretimsonu.id")
                        ->leftjoin("netsisstokkod", "uretimsonuurun.netsisstokkod_id", "=", "netsisstokkod.id")->get();
                    break;
            }
            if ($urunbilgi->count() > 1) {
                return array("durum" => true, "count" => $urunbilgi->count(), "urunbilgi" => $urunbilgi);
            }else if($urunbilgi->count()>0){
                $urun = $urunbilgi->first();
                if($urun->tipi==1){ //ürün ise parçaları,alt ve üst ürünleri bulunacak
                    $urun->uretimsonukullanilan = UretimSonuKullanilan::where('uretimsonu_id',$urun->uretimsonu_id)->get();
                    foreach ($urun->uretimsonukullanilan as $uretimsonukullanilan){
                        $uretimsonukullanilan->uretimurun = UretimUrun::find($uretimsonukullanilan->uretimurun_id);
                        $uretimsonukullanilan->netsisstokkod = NetsisStokKod::find($uretimsonukullanilan->netsisstokkod_id);
                        $uretimsonukullanilan->uretimuretici = UretimUretici::find($uretimsonukullanilan->uretimurun->uretimuretici_id);
                        $uretimsonukullanilan->uretimmarka = UretimUrunMarka::find($uretimsonukullanilan->uretimurun->uretimurunmarka_id);
                    }
                    $urun->usturun = UretimSonuUrun::find($urun->uretimsonuurun_id);
                    if($urun->usturun){
                        $urun->usturun->netsisstokkod=NetsisStokKod::find($urun->usturun->netsisstokkod_id);
                        $urun->usturun->uretimsonu=UretimSonu::find($urun->usturun->uretimsonu_id);
                    }
                    $urun->alturun = UretimSonuUrun::where('uretimsonuurun_id',$urun->id)->first();
                    if($urun->alturun){
                        $urun->alturun->netsisstokkod=NetsisStokKod::find($urun->alturun->netsisstokkod_id);
                        $urun->alturun->uretimsonu=UretimSonu::find($urun->alturun->uretimsonu_id);
                    }
                }else{ //parça ise kullanıldığı ürünler bulunacak
                    $urun->uretimsonukullanilan=UretimSonuKullanilan::where('uretimurun_id',$urun->id)->get(); //parçanın kullanıldığı ürünler
                    foreach ($urun->uretimsonukullanilan as $uretimsonukullanilan){
                        $uretimsonukullanilan->uretimsonu = UretimSonu::find($uretimsonukullanilan->uretimsonu_id);
                        $uretimsonukullanilan->uretimsonu->netsisstokkod = NetsisStokKod::find($uretimsonukullanilan->uretimsonu->netsisstokkod_id);
                        $uretimsonuurun = UretimSonuUrun::where('uretimsonu_id',$uretimsonukullanilan->uretimsonu_id)->get();
                        $serinolar="";
                        foreach ($uretimsonuurun as $urun_){
                            $serinolar.=($serinolar=="" ? "" : ", ").$urun_->serino;
                        }
                        $uretimsonukullanilan->serinolar=$serinolar;
                    }
                    if($urun->db_name!='MANAS' . date('Y')){
                        $connection = BackendController::AddNewConnection($urun->db_name);
                        $depokayidi = Sthar::on($connection)->where('INCKEYNO',$urun->inckeyno)
                            ->leftJoin('TBLCASABIT','TBLCASABIT.CARI_KOD','=','TBLSTHAR.STHAR_CARIKOD')
                            ->first(array(DB::raw('dbo.TRK(TBLSTHAR.STOK_KODU) as STOK_KODU'),'TBLSTHAR.FISNO','TBLSTHAR.STHAR_GCMIK','TBLSTHAR.STHAR_TARIH',
                                DB::raw('dbo.TRK(TBLSTHAR.STHAR_CARIKOD) as STHAR_CARIKOD'),'TBLSTHAR.STHAR_SIPNUM',
                                DB::raw('dbo.TRK(TBLSTHAR.STHAR_ACIKLAMA) as STHAR_ACIKLAMA'),DB::raw('dbo.TRK(TBLCASABIT.CARI_ISIM) as CARI_ISIM')));
                        BackendController::DeleteConnection($connection);
                    }else{
                        $depokayidi = Sthar::where('INCKEYNO',$urun->inckeyno)
                            ->leftJoin('TBLCASABIT','TBLCASABIT.CARI_KOD','=','TBLSTHAR.STHAR_CARIKOD')
                            ->first(array(DB::raw('dbo.TRK(TBLSTHAR.STOK_KODU) as STOK_KODU'),'TBLSTHAR.FISNO','TBLSTHAR.STHAR_GCMIK','TBLSTHAR.STHAR_TARIH',
                                DB::raw('dbo.TRK(TBLSTHAR.STHAR_CARIKOD) as STHAR_CARIKOD'),'TBLSTHAR.STHAR_SIPNUM',
                                DB::raw('dbo.TRK(TBLSTHAR.STHAR_ACIKLAMA) as STHAR_ACIKLAMA'),DB::raw('dbo.TRK(TBLCASABIT.CARI_ISIM) as CARI_ISIM')));
                    }
                    $depokayidi->STHAR_GCMIK = intval($depokayidi->STHAR_GCMIK);
                    $date = new DateTime($depokayidi->STHAR_TARIH);
                    $depokayidi->STHAR_TARIH =  $date->format('d-m-Y');
                    if(($depokayidi->FISNO)==null){
                        $urun->depokayitbilgi = $depokayidi->STHAR_ACIKLAMA .' - '. $depokayidi->STHAR_TARIH .' Tarihli - '.
                            $depokayidi->STHAR_GCMIK . ' Adet ';
                    }else{
                        $urun->depokayitbilgi = $depokayidi->FISNO .' - '. $depokayidi->STHAR_TARIH .' Tarihli - '.
                            $depokayidi->STHAR_GCMIK . ' Adet - ' . $depokayidi->STHAR_CARIKOD . ' - ' . $depokayidi->CARI_ISIM;
                    }
                }
                return array("durum" => true, "count" => $urunbilgi->count(), "urunbilgi" => $urun);
            } else {
                return array("durum" => false, "type" => "error", "title" => "Ürün Bilgisi Bulunamadı", "text" => "Arama Kriterine Ait Ürün Bilgisi Bulunamadı.");
            }
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            return Response::json(array('durum'=>false,"type" => "error", "title" => "Ürün Bilgisi Bulunamadı", "text" => str_replace("'","\'",$e->getMessage()) ));
        }
    }

    public function getUrunlistebilgigetir() {
        try {
            $id = Input::get('id');
            $kriter = Input::get('kriter');
            switch ($kriter) {
                case 1: // ürün
                    $urunbilgi = UretimSonuUrun::where('uretimsonuurun.id',$id)
                        ->select(array("uretimsonuurun.id",DB::raw('1 as tipi'), "uretimsonu.id as uretimsonu_id", "uretimsonuurun.serino", "netsisstokkod.kodu",
                            "netsisstokkod.adi as stokadi","uretimsonuurun.netsisstokkod_id","uretimsonuurun.db_name","uretimsonuurun.sirano","uretimsonuurun.inckeyno",
                            "uretimsonuurun.birimfiyat","uretimsonu.isemrino","uretimsonu.cikisdepo","uretimsonu.girisdepo","uretimsonu.geklenmetarihi",
                            "uretimsonuurun.uretimsonuurun_id"))
                        ->leftjoin("uretimsonu", "uretimsonuurun.uretimsonu_id", "=", "uretimsonu.id")
                        ->leftjoin("netsisstokkod", "uretimsonuurun.netsisstokkod_id", "=", "netsisstokkod.id")->first();
                    break;
                case 2: // parça
                    $urunbilgi = UretimUrun::where('uretimurun.id', $id)
                        ->select(array("uretimurun.id",DB::raw('2 as tipi'), "uretimurun.urunadi as stokadi", "uretimurun.barkod", "netsisstokkod.kodu",
                            "uretimurun.netsisstokkod_id","uretimuretici.ureticiadi","uretimurunmarka.markaadi","uretimurun.guretimtarihi","uretimurun.adet","uretimurun.kullanilan",
                            "uretimurun.kalan","uretimurun.gdepotarihi","uretimurun.db_name","uretimurun.faturano","uretimurun.birimfiyat","uretimurun.inckeyno",
                            "uretimurun.aciklama","uretimurun.geklenmetarihi","uretimurun.urunbarkod1","uretimurun.urunbarkod2","uretimurun.urunbarkod3"))
                        ->leftjoin("netsisstokkod", "uretimurun.netsisstokkod_id", "=", "netsisstokkod.id")
                        ->leftjoin("uretimuretici", "uretimurun.uretimuretici_id", "=", "uretimuretici.id")
                        ->leftjoin("uretimurunmarka", "uretimurun.uretimurunmarka_id", "=", "uretimurunmarka.id")->get();
                    break;
                default: // ürün
                    $urunbilgi = UretimSonuUrun::where('uretimsonuurun.id',$id)
                        ->select(array("uretimsonuurun.id",DB::raw('1 as tipi'), "uretimsonu.id as uretimsonu_id", "uretimsonuurun.serino", "netsisstokkod.kodu",
                            "netsisstokkod.adi as stokadi","uretimsonuurun.netsisstokkod_id","uretimsonuurun.db_name","uretimsonuurun.sirano","uretimsonuurun.inckeyno",
                            "uretimsonuurun.birimfiyat","uretimsonu.isemrino","uretimsonu.cikisdepo","uretimsonu.girisdepo","uretimsonu.geklenmetarihi",
                            "uretimsonuurun.uretimsonuurun_id"))
                        ->leftjoin("uretimsonu", "uretimsonuurun.uretimsonu_id", "=", "uretimsonu.id")
                        ->leftjoin("netsisstokkod", "uretimsonuurun.netsisstokkod_id", "=", "netsisstokkod.id")->first();
                    break;
            }
            if ($urunbilgi){
                $urun = $urunbilgi->first();
                if($urun->tipi==1){ //ürün ise parçaları,alt ve üst ürünleri bulunacak
                    $urun->uretimsonukullanilan = UretimSonuKullanilan::where('uretimsonu_id',$urun->uretimsonu_id)->get();
                    foreach ($urun->uretimsonukullanilan as $uretimsonukullanilan){
                        $uretimsonukullanilan->uretimurun = UretimUrun::find($uretimsonukullanilan->uretimurun_id);
                        $uretimsonukullanilan->netsisstokkod = NetsisStokKod::find($uretimsonukullanilan->netsisstokkod_id);
                        $uretimsonukullanilan->uretimuretici = UretimUretici::find($uretimsonukullanilan->uretimurun->uretimuretici_id);
                        $uretimsonukullanilan->uretimmarka = UretimUrunMarka::find($uretimsonukullanilan->uretimurun->uretimurunmarka_id);
                    }
                    $urun->usturun = UretimSonuUrun::find($urun->uretimsonuurun_id);
                    if($urun->usturun){
                        $urun->usturun->netsisstokkod=NetsisStokKod::find($urun->usturun->netsisstokkod_id);
                        $urun->usturun->uretimsonu=UretimSonu::find($urun->usturun->uretimsonu_id);
                    }
                    $urun->alturun = UretimSonuUrun::where('uretimsonuurun_id',$urun->id)->first();
                    if($urun->alturun){
                        $urun->alturun->netsisstokkod=NetsisStokKod::find($urun->alturun->netsisstokkod_id);
                        $urun->alturun->uretimsonu=UretimSonu::find($urun->alturun->uretimsonu_id);
                    }
                }else{ //parça ise kullanıldığı ürünler bulunacak
                    $urun->uretimsonukullanilan=UretimSonuKullanilan::where('uretimurun_id',$urun->id)->get(); //parçanın kullanıldığı ürünler
                    foreach ($urun->uretimsonukullanilan as $uretimsonukullanilan){
                        $uretimsonukullanilan->uretimsonu = UretimSonu::find($uretimsonukullanilan->uretimsonu_id);
                        $uretimsonukullanilan->uretimsonu->netsisstokkod = NetsisStokKod::find($uretimsonukullanilan->uretimsonu->netsisstokkod_id);
                        $uretimsonuurun = UretimSonuUrun::where('uretimsonu_id',$uretimsonukullanilan->uretimsonu_id)->get();
                        $serinolar="";
                        foreach ($uretimsonuurun as $urun_){
                            $serinolar.=($serinolar=="" ? "" : ", ").$urun_->serino;
                        }
                        $uretimsonukullanilan->serinolar=$serinolar;
                    }
                    if($urun->db_name!='MANAS' . date('Y')){
                        $connection = BackendController::AddNewConnection($urun->db_name);
                        $depokayidi = Sthar::on($connection)->where('INCKEYNO',$urun->inckeyno)
                            ->leftJoin('TBLCASABIT','TBLCASABIT.CARI_KOD','=','TBLSTHAR.STHAR_CARIKOD')
                            ->first(array(DB::raw('dbo.TRK(TBLSTHAR.STOK_KODU) as STOK_KODU'),'TBLSTHAR.FISNO','TBLSTHAR.STHAR_GCMIK','TBLSTHAR.STHAR_TARIH',
                                DB::raw('dbo.TRK(TBLSTHAR.STHAR_CARIKOD) as STHAR_CARIKOD'),'TBLSTHAR.STHAR_SIPNUM',
                                DB::raw('dbo.TRK(TBLSTHAR.STHAR_ACIKLAMA) as STHAR_ACIKLAMA'),DB::raw('dbo.TRK(TBLCASABIT.CARI_ISIM) as CARI_ISIM')));
                        BackendController::DeleteConnection($connection);
                    }else{
                        $depokayidi = Sthar::where('INCKEYNO',$urun->inckeyno)
                            ->leftJoin('TBLCASABIT','TBLCASABIT.CARI_KOD','=','TBLSTHAR.STHAR_CARIKOD')
                            ->first(array(DB::raw('dbo.TRK(TBLSTHAR.STOK_KODU) as STOK_KODU'),'TBLSTHAR.FISNO','TBLSTHAR.STHAR_GCMIK','TBLSTHAR.STHAR_TARIH',
                                DB::raw('dbo.TRK(TBLSTHAR.STHAR_CARIKOD) as STHAR_CARIKOD'),'TBLSTHAR.STHAR_SIPNUM',
                                DB::raw('dbo.TRK(TBLSTHAR.STHAR_ACIKLAMA) as STHAR_ACIKLAMA'),DB::raw('dbo.TRK(TBLCASABIT.CARI_ISIM) as CARI_ISIM')));
                    }
                    $depokayidi->STHAR_GCMIK = intval($depokayidi->STHAR_GCMIK);
                    $date = new DateTime($depokayidi->STHAR_TARIH);
                    $depokayidi->STHAR_TARIH =  $date->format('d-m-Y');
                    if(($depokayidi->FISNO)==null){
                        $urun->depokayitbilgi = $depokayidi->STHAR_ACIKLAMA .' - '. $depokayidi->STHAR_TARIH .' Tarihli - '.
                            $depokayidi->STHAR_GCMIK . ' Adet ';
                    }else{
                        $urun->depokayitbilgi = $depokayidi->FISNO .' - '. $depokayidi->STHAR_TARIH .' Tarihli - '.
                            $depokayidi->STHAR_GCMIK . ' Adet - ' . $depokayidi->STHAR_CARIKOD . ' - ' . $depokayidi->CARI_ISIM;
                    }
                }
                return array("durum" => true, "urunbilgi" => $urun);
            }else {
                return array("durum" => false, "type" => "error", "title" => "Ürün Bilgisi Bulunamadı", "text" => "Arama Kriterine Ait Ürün Bilgisi Bulunamadı.");
            }
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            return Response::json(array('durum'=>false,"type" => "error", "title" => "Ürün Bilgisi Bulunamadı", "text" => str_replace("'","\'",$e->getMessage()) ));
        }
    }

}
