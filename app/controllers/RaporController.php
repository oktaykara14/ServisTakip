<?php
//transaction tamamlandı
class RaporController extends BackendController {

    public static function getIndex(){
        $netsiscariid=Auth::user()->netsiscari_id;
        $sube=Sube::whereIn('netsiscari_id',$netsiscariid)->where('aktif',1)->first();
        if($sube){
            $raporlar=Rapor::where('durum',1)->where('tipi',2)->get();
        }else{
            $raporlar=Rapor::where('durum',1)->whereIn('tipi',array(0,1))->get();
        }
        return View::make('rapor.raporlar',array('raporlar'=>$raporlar))->with(array('title'=>'Rapor Bilgisi'));
    }

    public static function getRaporkriter(){
        try {
            $id=Input::get('id');
            $kriterler = RaporKriter::where('rapor_id', $id)->get();
            if ($kriterler->count() > 0) {
                $netsiscariid=Auth::user()->netsiscari_id;
                $sube = Sube::whereIn('netsiscari_id', $netsiscariid)->where('aktif', 1)->first();
                foreach ($kriterler as $kriter) {
                    switch ($kriter->tipi) {
                        case 'select':
                        case 'select2' :
                            $model = $kriter->model;
                            $aciklayici = $kriter->aciklayicikolon;
                            $aciklayici2 = $kriter->aciklayicikolon2;
                            $join = $kriter->modeljoin;
                            if ($model == 'Sube') {
                                $kriter->data = $model::where('aktif', 1)->get();
                            } else if ($model == 'NetsisCari') {
                                if ($sube) {
                                    $sube->netsiscari = NetsisCari::find($sube->netsiscari_id);
                                    $subeyetkili = SubeYetkili::where('kullanici_id', Auth::user()->id)->where('aktif', 1)->get(array('netsiscari_id'))->toArray();
                                    $kriter->data = NetsisCari::where('caridurum', 'A')->whereIn('caritipi', array('A', 'D'))
                                        ->where(function ($query) use ($subeyetkili, $sube) {
                                            $query->whereIn('id', $subeyetkili)->orwhereIn('subekodu', array(-1, $sube->subekodu));
                                        })
                                        ->whereNotIn('carikod', (function ($query) use ($sube) {
                                            $query->select('carikod')->from('kodharichar')->where('subekodu', $sube->subekodu);
                                        }))
                                        ->whereNotIn('id', array($sube->netsiscari_id))
                                        ->orderBy('cariadi', 'asc')->get();
                                } else {
                                    $kriter->data = NetsisCari::where('caridurum', 'A')->whereIn('caritipi', array('A', 'D'))->orderBy('cariadi', 'asc')->get();
                                }
                            } else {
                                $kriter->data = $model::all();
                            }
                            $kolon = $kriter->kolon;
                            if (is_null($kriter->modeljoin)) {
                                foreach ($kriter->data as $data) {
                                    $data->id = $data->$kolon;
                                    $data->value = $data->$aciklayici.($aciklayici2!=null ? '('.$data->$aciklayici2.')' : '');
                                }
                            } else {
                                foreach ($kriter->data as $data) {
                                    $datajoin = $data->$join;
                                    $data->id = $data->$kolon;
                                    $data->value = $datajoin->$aciklayici.($aciklayici2!=null ? '('.$datajoin->$aciklayici2.')' : '');
                                }
                            }
                            break;
                        case 'multiselect':
                            $model = $kriter->model;
                            $join = $kriter->modeljoin;
                            $aciklayici = $kriter->aciklayicikolon;
                            $aciklayici2 = $kriter->aciklayicikolon2;
                            if ($model == 'NetsisCari') {
                                if ($sube) {
                                    $sube->netsiscari = NetsisCari::find($sube->netsiscari_id);
                                    $subeyetkili = SubeYetkili::where('kullanici_id', Auth::user()->id)->where('aktif', 1)->get(array('netsiscari_id'))->toArray();
                                    $kriter->data = NetsisCari::where('caridurum', 'A')->whereIn('caritipi', array('A', 'D'))
                                        ->where(function ($query) use ($subeyetkili, $sube) {
                                            $query->whereIn('id', $subeyetkili)->orwhereIn('subekodu', array(-1, $sube->subekodu));
                                        })
                                        ->whereNotIn('carikod', (function ($query) use ($sube) {
                                            $query->select('carikod')->from('kodharichar')->where('subekodu', $sube->subekodu);
                                        }))
                                        ->whereNotIn('id', array($sube->netsiscari_id))
                                        ->orderBy('cariadi', 'asc')->get();
                                } else {
                                    $kriter->data = NetsisCari::where('caridurum', 'A')->whereIn('caritipi', array('A', 'D'))->orderBy('cariadi', 'asc')->get();
                                }
                            } else {
                                $kriter->data = $model::where('id', '<>', 0)->get();
                            }
                            if (is_null($kriter->modeljoin)) {
                                foreach ($kriter->data as $data) {
                                    $data->value = $data->$aciklayici.($aciklayici2!=null ? '('.$data->$aciklayici2.')' : '');
                                }
                            } else {
                                foreach ($kriter->data as $data) {
                                    $datajoin = $data->$join;
                                    $data->value = $datajoin->$aciklayici.($aciklayici2!=null ? '('.$data->$aciklayici2.')' : '');
                                }
                            }
                            break;
                        default:
                            $kriter->data = '';
                            break;
                    }
                }
                return Response::json(array('durum' => true, 'kriterler' => $kriterler));
            }
            return Response::json(array('durum' => false,'title'=>'Rapor Bilgi Hatası','text'=>'Seçilen Rapor Adı için Arama Kriterleri Getirilemedi','type'=>'error'));
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum' => false,'title'=>'Rapor Bilgi Hatası','text'=>str_replace("'","\'",$e->getMessage()),'type'=>'error'));
        }
    }

    public function getSayacbilgi() {
        $servisler=Servis::all();
        return View::make('rapor.sayacbilgi',array('servisler'=>$servisler))->with(array('title'=>'İki Tarih Arası Sayaç Bilgisi'));
    }

    public function getSayacadi($servisid)
    {
        try {
            $sayacadlari=array();
            switch ($servisid) {
                case 0:
                    $sayacadlari = Sayacadi::all();
                    break;
                case 1:
                    $sayacadlari = SayacAdi::where('sayactur_id', 1)->get();
                    break;
                case 2:
                    $sayacadlari = SayacAdi::where('sayactur_id', 2)->get();
                    break;
                case 3:
                    $sayacadlari = SayacAdi::where('sayactur_id', 3)->get();
                    break;
                case 4:
                    $sayacadlari = SayacAdi::where('sayactur_id', 4)->get();
                    break;
                case 5:
                    $sayacadlari = SayacAdi::where('sayactur_id', 5)->get();
                    break;
                case 6:
                    $sayacadlari = SayacAdi::whereIn('sayactur_id', array(1, 2, 3, 4))->get();
                    break;
            }
            foreach ($sayacadlari as $sayacadi) {
                $sayacadi->value = $sayacadi->sayacadi;
            }
            return Response::json(array('durum' => true, 'degisecek' => $sayacadlari));
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum' => false,'title'=>'Rapor Bilgi Hatası','text'=>str_replace("'","\'",$e->getMessage()),'type'=>'error'));
        }
    }

    public function postIndex(){
        try {
            $rapor = Rapor::find(Input::get('raporadi'));
            $raporadi = Str::slug($rapor->adi);
            $export = Input::get('export');
            $raporkriter = RaporKriter::where('rapor_id', $rapor->id)->get();
            $kriterler = array();
            foreach ($raporkriter as $kriter) {
                $kriter->value = Input::get($kriter->kriteradi);
                switch ($kriter->tipi) {
                    case 'select':
                    case 'select2':
                        $kriterler[$kriter->kriteradi] = $kriter->value;
                        break;
                    case 'multiselect':
                        $list = "";
                        foreach ($kriter->value as $value) {
                            $list .= ($list == "" ? "" : ",") . $value;
                        }
                        $kriter->value = $list;
                        $kriterler[$kriter->kriteradi] = $kriter->value;
                        break;
                    case 'daterange':
                        $tarih = explode(' - ', $kriter->value);
                        $date = explode('.', $tarih[0]);
                        $ilktarih = date('Y-m-d', mktime(0, 0, 0, $date[1], $date[0], $date[2]));
                        $date = explode('.', $tarih[1]);
                        $sontarih = date('Y-m-d', mktime(0, 0, 0, $date[1], $date[0], $date[2]));
                        $kriterler['ilktarih'] = $ilktarih;
                        $kriterler['sontarih'] = $sontarih;
                        break;
                    default:
                        break;
                }
            }

            JasperPHP::process(public_path('reports/' . $rapor->raporadi . '/' . $rapor->raporadi . '.jasper'), public_path('reports/outputs/' . $rapor->raporadi . '/' . $raporadi),
                array($export), $kriterler,
                Config::get('database.connections.report'))->execute();
            if ($export == 'pdf') {
                header("Content-type:application/pdf");
                header("Content-Disposition:inline;filename=".$raporadi.".".$export."");
                //header("Content-Disposition:attachment;filename=" . $raporadi . "." . $export . "");
            } else if ($export == 'xls') {
                header("Content-Type:   application/vnd.ms-excel");
                header("Content-Disposition: inline; filename=".$raporadi.".".$export."");
            } else {
                //header('Content-Type: application/octet-stream');
                header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
                header("Content-Disposition: inline; filename=".$raporadi.".".$export."");
            }
            readfile("reports/outputs/" . $rapor->raporadi . "/" . $raporadi . "." . $export . "");
            File::delete("reports/outputs/" . $rapor->raporadi . "/" . $raporadi . "." . $export . "");
            $raporlar=Rapor::all();
            return View::make('rapor.raporlar', array('raporlar' => $raporlar))->with(array('title' => 'Rapor Bilgisi'));
        } catch (Exception $e) {
            Log::error($e);
            $raporlar=Rapor::all();
            //return Redirect::back()->withInput()->with(array('mesaj' => 'true'
            return View::make('rapor.raporlar', array('raporlar' => $raporlar))->with(array('mesaj' => 'true','title'=>'Rapor Bilgi Hatası','text'=>str_replace("'","\'",$e->getMessage()),'type'=>'error'));
        }
    }

    public function getDegisenler($servisid)
    {
        try {
            $degisenler=array();
            switch ($servisid) {
                case 0:
                    $degisenler = Degisenler::all();
                    break;
                case 1:
                    $degisenler = Degisenler::where('sayactur_id', 1)->get();
                    break;
                case 2:
                    $degisenler = Degisenler::where('sayactur_id', 2)->get();
                    break;
                case 3:
                    $degisenler = Degisenler::where('sayactur_id', 3)->get();
                    break;
                case 4:
                    $degisenler = Degisenler::where('sayactur_id', 4)->get();
                    break;
                case 5:
                    $degisenler = Degisenler::where('sayactur_id', 5)->get();
                    break;
                case 6:
                    $degisenler = Degisenler::whereIn('sayactur_id', array(1, 2, 3, 4))->get();
                    break;
            }
            foreach ($degisenler as $degisen) {
                $degisen->value = $degisen->tanim;
            }
            return Response::json(array('durum' => true, 'degisecek' => $degisenler));
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum' => false,'title'=>'Rapor Bilgi Hatası','text'=>str_replace("'","\'",$e->getMessage()),'type'=>'error'));
        }
    }

    public function getArizakodlar($servisid)
    {
        try {
            $arizalar=array();
            switch ($servisid) {
                case 0:
                    $arizalar = ArizaKod::all();
                    break;
                case 1:
                    $arizalar = ArizaKod::where('sayactur_id', 1)->get();
                    break;
                case 2:
                    $arizalar = ArizaKod::where('sayactur_id', 2)->get();
                    break;
                case 3:
                    $arizalar = ArizaKod::where('sayactur_id', 3)->get();
                    break;
                case 4:
                    $arizalar = ArizaKod::where('sayactur_id', 4)->get();
                    break;
                case 5:
                    $arizalar = ArizaKod::where('sayactur_id', 5)->get();
                    break;
                case 6:
                    $arizalar = ArizaKod::whereIn('sayactur_id', array(1, 2, 3, 4))->get();
                    break;
            }
            foreach ($arizalar as $ariza) {
                $ariza->value = $ariza->tanim;
            }
            return Response::json(array('durum' => true, 'degisecek' => $arizalar));
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum' => false,'title'=>'Rapor Bilgi Hatası','text'=>str_replace("'","\'",$e->getMessage()),'type'=>'error'));
        }
    }

}
