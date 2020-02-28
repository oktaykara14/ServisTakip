<?php
// ana ekran gelen giden bilgisi güncellenecek
class MainController extends BackendController {

    public function getIndex() {

        //\Debugbar::disable();
        return View::make('index')->with(array('title'=>'Ana Sayfa'));
    }

    public static function getGelenBiten($date,$servisid,$tip,$cariid=array())
    {
        $list=array();
        setlocale(LC_ALL, 'tr_TR.UTF-8');
        switch ($tip) {
            case "1": //kaç ay geçmişse aylara göre ayıracağız
                $date=explode('-',$date);
                $first_day = strtotime(date('Y-m-d H:i:s', mktime(0, 0, 0, 1, 1, $date[0])));
                $now=time(); //suan
                $fark=$now-$first_day;
                $i = 1;
                if($fark<86400) //bugün içinde saatlere böl
                {
                    $gun_time = strtotime(date('Y-m-d H:i:s', mktime(0, 0, 0, 1, 1, $date[0]))); // saat dakika saniye ay gün yıl
                    while($gun_time<$now)
                    {
                        $other = date('Y-m-d H:i:s', mktime($i, 0, -1, 1, 1, $date[0]));
                        $gun = date('Y-m-d H:i:s',$gun_time);
                        if($servisid=="6"){
                            $sube = Sube::whereIn('netsiscari_id',$cariid)->where('aktif',1)->first();
                            if($sube)
                                $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->where('servis_id',$servisid)->where('subekodu',$sube->subekodu)->where('durum','<>',2)->sum('adet');
                            else
                                $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->where('servis_id',$servisid)->whereNotIn('depokodu',array(6,24,25))->where('durum','<>',2)->sum('adet');
                            if($sube){
                                $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)->where('subekodu',$sube->subekodu)
                                    ->where(function($query){$query->whereNotNull('onaylanmatarihi')->orWhereNotNull('gerigonderimtarihi')
                                        ->orWhereNotNull('hurdalamatarihi');})->count();
                            }else{
                                $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)
                                    ->where(function($query){$query->whereNotNull('onaylanmatarihi')->orWhereNotNull('gerigonderimtarihi')
                                        ->orWhereNotNull('hurdalamatarihi');})->count();
                            }
                        }else{
                            $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->whereIn('depokodu',array(6,24,25))->where('servis_id', $servisid)->where('durum','<>',2)->sum('adet');
                            $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)
                                ->where(function($query){$query->whereNotNull('depoteslimtarihi')->orWhereNotNull('gerigonderimtarihi')
                                    ->orWhereNotNull('hurdalamatarihi');})->count();
                        }
                        array_push($list,array('deger'=>date('H',strtotime($gun)),'bolum'=>'Saat','date'=>date('D-H',strtotime($gun)),'depogelen'=>$depogelen,'biten'=>$biten));
                        $gun_time = strtotime(date('Y-m-d H:i:s', mktime($i, 0, 0, 1, 1, $date[0])));
                        $i++;
                    }
                }else if($fark<604800) // 1 haftadan az ise gunlere böl
                {
                    $gun_time = strtotime(date('Y-m-d H:i:s', mktime(0, 0, 0, 1, 1, $date[0])));
                    while($gun_time<$now)
                    {
                        $other = date('Y-m-d H:i:s', mktime(0, 0, -1, 1, $i+1, $date[0]));
                        $gun = date('Y-m-d H:i:s',$gun_time);
                        if($servisid=="6"){
                            $sube = Sube::whereIn('netsiscari_id',$cariid)->where('aktif',1)->first();
                            if($sube)
                                $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->where('servis_id',$servisid)->where('subekodu',$sube->subekodu)->where('durum','<>',2)->sum('adet');
                            else
                                $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->where('servis_id',$servisid)->whereNotIn('depokodu',array(6,24,25))->where('durum','<>',2)->sum('adet');
                            if($sube){
                                $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)->where('subekodu',$sube->subekodu)
                                    ->where(function($query){$query->whereNotNull('onaylanmatarihi')->orWhereNotNull('gerigonderimtarihi')
                                        ->orWhereNotNull('hurdalamatarihi');})->count();
                            }else{
                                $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)
                                    ->where(function($query){$query->whereNotNull('onaylanmatarihi')->orWhereNotNull('gerigonderimtarihi')
                                        ->orWhereNotNull('hurdalamatarihi');})->count();
                            }

                        }else{
                            $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->whereIn('depokodu',array(6,24,25))->where('servis_id', $servisid)->where('durum','<>',2)->sum('adet');
                            $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)
                                ->where(function($query){$query->whereNotNull('depoteslimtarihi')->orWhereNotNull('gerigonderimtarihi')
                                    ->orWhereNotNull('hurdalamatarihi');})->count();
                        }
                        array_push($list,array('deger'=>strftime("%B %e", strtotime($gun)),'bolum'=>'Gun','date'=>date('d-m-Y',strtotime($gun)),'depogelen'=>$depogelen,'biten'=>$biten));
                        $gun_time = strtotime(date('Y-m-d H:i:s', mktime(0, 0, 0, 1, $i+1, $date[0])));
                        $i++;
                    }
                }else if($fark<1209600) // 2 haftadan az ise gunlere böl
                {
                    $gun_time = strtotime(date('Y-m-d H:i:s', mktime(0, 0, 0, 1, 1, $date[0])));
                    while($gun_time<$now)
                    {
                        $other = date('Y-m-d H:i:s', mktime(0, 0, -1, 1, $i+1, $date[0]));
                        $gun = date('Y-m-d H:i:s',$gun_time);
                        if($servisid=="6"){
                            $sube = Sube::whereIn('netsiscari_id',$cariid)->where('aktif',1)->first();
                            if($sube)
                                $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->where('servis_id',$servisid)->where('subekodu',$sube->subekodu)->where('durum','<>',2)->sum('adet');
                            else
                                $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->where('servis_id',$servisid)->whereNotIn('depokodu',array(6,24,25))->where('durum','<>',2)->sum('adet');
                            if($sube){
                                $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)->where('subekodu',$sube->subekodu)
                                    ->where(function($query){$query->whereNotNull('onaylanmatarihi')->orWhereNotNull('gerigonderimtarihi')
                                        ->orWhereNotNull('hurdalamatarihi');})->count();
                            }else{
                                $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)
                                    ->where(function($query){$query->whereNotNull('onaylanmatarihi')->orWhereNotNull('gerigonderimtarihi')
                                        ->orWhereNotNull('hurdalamatarihi');})->count();
                            }
                        }else{
                            $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->whereIn('depokodu',array(6,24,25))->where('servis_id', $servisid)->where('durum','<>',2)->sum('adet');
                            $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)
                                ->where(function($query){$query->whereNotNull('depoteslimtarihi')->orWhereNotNull('gerigonderimtarihi')
                                    ->orWhereNotNull('hurdalamatarihi');})->count();
                        }
                        array_push($list,array('deger'=>strftime("%B %e", strtotime($gun)),'bolum'=>'Gun','date'=>date('d-m',strtotime($gun)),'depogelen'=>$depogelen,'biten'=>$biten));
                        $gun_time = strtotime(date('Y-m-d H:i:s', mktime(0, 0, 0, 1, $i+1, $date[0])));
                        $i++;
                    }
                }else if($fark<2592000) // 1 aydan az ise haftalara böl
                {
                    $i = 0;
                    $gun_time = $first_day; // saat dakika saniye ay gün yıl
                    $day = date('w', $first_day);
                    while($gun_time<$now)
                    {
                        $j=(8-$day)+(7*$i);
                        $other = date('Y-m-d H:i:s', mktime(0, 0, -1, 1, 1+$j, $date[0]));
                        $gun = date('Y-m-d H:i:s',$gun_time);
                        if($servisid=="6"){
                            $sube = Sube::whereIn('netsiscari_id',$cariid)->where('aktif',1)->first();
                            if($sube)
                                $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->where('servis_id',$servisid)->where('subekodu',$sube->subekodu)->where('durum','<>',2)->sum('adet');
                            else
                                $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->where('servis_id',$servisid)->whereNotIn('depokodu',array(6,24,25))->where('durum','<>',2)->sum('adet');
                            if($sube){
                                $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)->where('subekodu',$sube->subekodu)
                                    ->where(function($query){$query->whereNotNull('onaylanmatarihi')->orWhereNotNull('gerigonderimtarihi')
                                        ->orWhereNotNull('hurdalamatarihi');})->count();
                            }else{
                                $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)
                                    ->where(function($query){$query->whereNotNull('onaylanmatarihi')->orWhereNotNull('gerigonderimtarihi')
                                        ->orWhereNotNull('hurdalamatarihi');})->count();
                            }
                        }else{
                            $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->whereIn('depokodu',array(6,24,25))->where('servis_id', $servisid)->where('durum','<>',2)->sum('adet');
                            $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)
                                ->where(function($query){$query->whereNotNull('depoteslimtarihi')->orWhereNotNull('gerigonderimtarihi')
                                    ->orWhereNotNull('hurdalamatarihi');})->count();
                        }
                        array_push($list,array('deger'=>date('W',strtotime($gun)),'bolum'=>'Hafta','date'=>date('W-Y',strtotime($gun)),'depogelen'=>$depogelen,'biten'=>$biten));
                        $gun_time = strtotime(date('Y-m-d H:i:s', mktime(0, 0, 0, 1, 1+$j, $date[0])));
                        $i++;
                    }
                }else if($fark<7776000) // 3 aydan az ise haftalara böl
                {
                    $i = 0;
                    $gun_time = $first_day; // saat dakika saniye ay gün yıl
                    $day = date('w', $first_day);
                    while($gun_time<$now)
                    {
                        $j=(8-$day)+(7*$i);
                        $other = date('Y-m-d H:i:s', mktime(0, 0, -1, 1, 1+$j, $date[0]));
                        $gun = date('Y-m-d H:i:s',$gun_time);
                        if($servisid=="6") {
                            $sube = Sube::whereIn('netsiscari_id',$cariid)->where('aktif',1)->first();
                            if($sube)
                                $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->where('servis_id',$servisid)->where('subekodu',$sube->subekodu)->where('durum','<>',2)->sum('adet');
                            else
                                $depogelen = DepoGelen::whereBetween('tarih', array($gun, $other))->where('servis_id',$servisid)->whereNotIn('depokodu',array(6,24,25))->where('durum', '<>', 2)->sum('adet');
                            if($sube){
                                $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)->where('subekodu',$sube->subekodu)
                                    ->where(function($query){$query->whereNotNull('onaylanmatarihi')->orWhereNotNull('gerigonderimtarihi')
                                        ->orWhereNotNull('hurdalamatarihi');})->count();
                            }else{
                                $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)
                                    ->where(function($query){$query->whereNotNull('onaylanmatarihi')->orWhereNotNull('gerigonderimtarihi')
                                        ->orWhereNotNull('hurdalamatarihi');})->count();
                            }
                        }else{
                            $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->whereIn('depokodu',array(6,24,25))->where('servis_id', $servisid)->where('durum','<>',2)->sum('adet');
                            $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)
                                ->where(function($query){$query->whereNotNull('depoteslimtarihi')->orWhereNotNull('gerigonderimtarihi')
                                    ->orWhereNotNull('hurdalamatarihi');})->count();
                        }
                        array_push($list,array('deger'=>date('W',strtotime($gun)),'bolum'=>'Hafta','date'=>date('W-Y',strtotime($gun)),'depogelen'=>$depogelen,'biten'=>$biten));
                        $gun_time = strtotime(date('Y-m-d H:i:s', mktime(0, 0, 0, 1, $j+1, $date[0])));
                        $i++;
                    }
                }elseif($fark<15552000) // 6 aydan az ise aylara böl
                {
                    $gun_time = strtotime(date('Y-m-d H:i:s', mktime(0, 0, 0, 1, 1, $date[0])));
                    while($gun_time<$now)
                    {
                        $other = date('Y-m-d H:i:s', mktime(0, 0, -1, $i+1, 1, $date[0]));
                        $gun = date('Y-m-d H:i:s',$gun_time);
                        if($servisid=="6"){
                            $sube = Sube::whereIn('netsiscari_id',$cariid)->where('aktif',1)->first();
                            if($sube)
                                $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->where('servis_id',$servisid)->where('subekodu',$sube->subekodu)->where('durum','<>',2)->sum('adet');
                            else
                                $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->where('servis_id',$servisid)->whereNotIn('depokodu',array(6,24,25))->where('durum','<>',2)->sum('adet');
                            if($sube){
                                $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)->where('subekodu',$sube->subekodu)
                                    ->where(function($query){$query->whereNotNull('onaylanmatarihi')->orWhereNotNull('gerigonderimtarihi')
                                        ->orWhereNotNull('hurdalamatarihi');})->count();
                            }else{
                                $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)
                                    ->where(function($query){$query->whereNotNull('onaylanmatarihi')->orWhereNotNull('gerigonderimtarihi')
                                        ->orWhereNotNull('hurdalamatarihi');})->count();
                            }
                        }else{
                            $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->whereIn('depokodu',array(6,24,25))->where('servis_id', $servisid)->where('durum','<>',2)->sum('adet');
                            $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)
                                ->where(function($query){$query->whereNotNull('depoteslimtarihi')->orWhereNotNull('gerigonderimtarihi')
                                    ->orWhereNotNull('hurdalamatarihi');})->count();
                        }
                        array_push($list,array('deger'=>date('m',strtotime($gun)),'bolum'=>'Ay','date'=>date('m-Y',strtotime($gun)),'depogelen'=>$depogelen,'biten'=>$biten));
                        $gun_time = strtotime(date('Y-m-d H:i:s', mktime(0, 0, 0, $i+1, 1, $date[0])));
                        $i++;
                    }
                }elseif($fark<31104000) // 1 yıldan az ise aylara böl
                {
                    $gun_time = strtotime(date('Y-m-d H:i:s', mktime(0, 0, 0, 1, 1, $date[0])));
                    while($gun_time<$now)
                    {
                        $other = date('Y-m-d H:i:s', mktime(0, 0, -1, $i+1, 1, $date[0]));
                        $gun = date('Y-m-d H:i:s',$gun_time);
                        if($servisid=="6"){
                            $sube = Sube::whereIn('netsiscari_id',$cariid)->where('aktif',1)->first();
                            if($sube)
                                $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->where('servis_id',$servisid)->where('subekodu',$sube->subekodu)->where('durum','<>',2)->sum('adet');
                            else
                                $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->where('servis_id',$servisid)->whereNotIn('depokodu',array(6,24,25))->where('durum','<>',2)->sum('adet');
                            if($sube){
                                $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)->where('subekodu',$sube->subekodu)
                                    ->where(function($query){$query->whereNotNull('onaylanmatarihi')->orWhereNotNull('gerigonderimtarihi')
                                        ->orWhereNotNull('hurdalamatarihi');})->count();
                            }else{
                                $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)
                                    ->where(function($query){$query->whereNotNull('onaylanmatarihi')->orWhereNotNull('gerigonderimtarihi')
                                        ->orWhereNotNull('hurdalamatarihi');})->count();
                            }
                        }else{
                            $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->whereIn('depokodu',array(6,24,25))->where('servis_id', $servisid)->where('durum','<>',2)->sum('adet');
                            $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)
                                ->where(function($query){$query->whereNotNull('depoteslimtarihi')->orWhereNotNull('gerigonderimtarihi')
                                    ->orWhereNotNull('hurdalamatarihi');})->count();
                        }
                        array_push($list,array('deger'=>date('m',strtotime($gun)),'bolum'=>'Ay','date'=>date('m-Y',strtotime($gun)),'depogelen'=>$depogelen,'biten'=>$biten));
                        $gun_time = strtotime(date('Y-m-d H:i:s', mktime(0, 0, 0, $i+1, 1, $date[0])));
                        $i++;
                    }
                }else{ //tamamı aylara böl
                    $gun_time = strtotime(date('Y-m-d H:i:s', mktime(0, 0, 0, 1, 1, $date[0])));
                    while($gun_time<$now)
                    {
                        $other = date('Y-m-d H:i:s', mktime(0, 0, -1, $i+1, 1, $date[0]));
                        $gun = date('Y-m-d H:i:s',$gun_time);
                        if($servisid=="6"){
                            $sube = Sube::whereIn('netsiscari_id',$cariid)->where('aktif',1)->first();
                            if($sube)
                                $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->where('servis_id',$servisid)->where('subekodu',$sube->subekodu)->where('durum','<>',2)->sum('adet');
                            else
                                $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->where('servis_id',$servisid)->whereNotIn('depokodu',array(6,24,25))->where('durum','<>',2)->sum('adet');
                            if($sube){
                                $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)->where('subekodu',$sube->subekodu)
                                    ->where(function($query){$query->whereNotNull('onaylanmatarihi')->orWhereNotNull('gerigonderimtarihi')
                                        ->orWhereNotNull('hurdalamatarihi');})->count();
                            }else{
                                $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)
                                    ->where(function($query){$query->whereNotNull('onaylanmatarihi')->orWhereNotNull('gerigonderimtarihi')
                                        ->orWhereNotNull('hurdalamatarihi');})->count();
                            }
                        }else{
                            $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->whereIn('depokodu',array(6,24,25))->where('servis_id', $servisid)->where('durum','<>',2)->sum('adet');
                            $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)
                                ->where(function($query){$query->whereNotNull('depoteslimtarihi')->orWhereNotNull('gerigonderimtarihi')
                                    ->orWhereNotNull('hurdalamatarihi');})->count();
                        }
                        array_push($list,array('deger'=>date('m',strtotime($gun)),'bolum'=>'Ay','date'=>date('m-Y',strtotime($gun)),'depogelen'=>$depogelen,'biten'=>$biten));
                        $gun_time = strtotime(date('Y-m-d H:i:s', mktime(0, 0, 0, $i+1, 1, $date[0])));
                        $i++;
                    }
                }
                break; // bu yıl
            case "2": //bugün saatlere böl
                $date=explode('-',$date);
                $first_day = strtotime(date('Y-m-d H:i:s', mktime(0, 0, 0, $date[1], $date[2], $date[0])));
                $now=time(); //suan
                $i = 1;
                $gun_time = $first_day; // saat dakika saniye ay gün yıl
                while($gun_time<$now)
                {
                    $other = date('Y-m-d H:i:s', mktime($i, 0, -1, $date[1], $date[2], $date[0]));
                    $gun = date('Y-m-d H:i:s',$gun_time);
                    if($servisid=="6"){
                        $sube = Sube::whereIn('netsiscari_id',$cariid)->where('aktif',1)->first();
                        if($sube)
                            $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->where('servis_id',$servisid)->where('subekodu',$sube->subekodu)->where('durum','<>',2)->sum('adet');
                        else
                            $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->where('servis_id',$servisid)->whereNotIn('depokodu',array(6,24,25))->where('durum','<>',2)->sum('adet');
                        if($sube){
                            $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)->where('subekodu',$sube->subekodu)
                                ->where(function($query){$query->whereNotNull('onaylanmatarihi')->orWhereNotNull('gerigonderimtarihi')
                                    ->orWhereNotNull('hurdalamatarihi');})->count();
                        }else{
                            $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)
                                ->where(function($query){$query->whereNotNull('onaylanmatarihi')->orWhereNotNull('gerigonderimtarihi')
                                    ->orWhereNotNull('hurdalamatarihi');})->count();
                        }
                    }else{
                        $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->whereIn('depokodu',array(6,24,25))->where('servis_id', $servisid)->where('durum','<>',2)->sum('adet');
                        $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)
                            ->where(function($query){$query->whereNotNull('depoteslimtarihi')->orWhereNotNull('gerigonderimtarihi')
                                ->orWhereNotNull('hurdalamatarihi');})->count();
                    }
                    array_push($list,array('deger'=>date('H',strtotime($gun)),'bolum'=>'Saat','date'=>date('H:i',strtotime($gun)),'depogelen'=>$depogelen,'biten'=>$biten));
                    $gun_time = strtotime(date('Y-m-d H:i:s', mktime($i, 0, 0, $date[1], $date[2], $date[0])));
                    $i++;
                }
                break; // bugün
            case "3": //bu hafta gunlere böl
                $date=explode('-',$date);
                $first_day = strtotime(date('Y-m-d H:i:s', mktime(0, 0, 0, $date[1], $date[2], $date[0])));
                $now=time(); //suan
                $i = 1;
                $gun_time = $first_day; // saat dakika saniye ay gün yıl
                while($gun_time<$now)
                {
                    $other = date('Y-m-d H:i:s', mktime(0, 0, -1, $date[1], $date[2]+$i, $date[0]));
                    $gun = date('Y-m-d H:i:s',$gun_time);
                    if($servisid=="6"){
                        $sube = Sube::whereIn('netsiscari_id',$cariid)->where('aktif',1)->first();
                        if($sube)
                            $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->where('servis_id',$servisid)->where('subekodu',$sube->subekodu)->where('durum','<>',2)->sum('adet');
                        else
                            $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->where('servis_id',$servisid)->whereNotIn('depokodu',array(6,24,25))->where('durum','<>',2)->sum('adet');
                        if($sube){
                            $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)->where('subekodu',$sube->subekodu)
                                ->where(function($query){$query->whereNotNull('onaylanmatarihi')->orWhereNotNull('gerigonderimtarihi')
                                    ->orWhereNotNull('hurdalamatarihi');})->count();
                        }else{
                            $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)
                                ->where(function($query){$query->whereNotNull('onaylanmatarihi')->orWhereNotNull('gerigonderimtarihi')
                                    ->orWhereNotNull('hurdalamatarihi');})->count();
                        }
                    }else{
                        $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->whereIn('depokodu',array(6,24,25))->where('servis_id', $servisid)->where('durum','<>',2)->sum('adet');
                        $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)
                            ->where(function($query){$query->whereNotNull('depoteslimtarihi')->orWhereNotNull('gerigonderimtarihi')
                                ->orWhereNotNull('hurdalamatarihi');})->count();
                    }
                    array_push($list,array('deger'=>date('w',strtotime($gun)),'bolum'=>'Gun','date'=>date('d-m-Y',strtotime($gun)),'depogelen'=>$depogelen,'biten'=>$biten));
                    $gun_time = strtotime(date('Y-m-d H:i:s', mktime(0, 0, 0, $date[1], $date[2]+$i, $date[0])));
                    $i++;
                }
                ; break; // son 1 hafta
            case "4": // son 2 hafta gunlere böl
                $date=explode('-',$date);
                $first_day = strtotime(date('Y-m-d H:i:s', mktime(0, 0, 0, $date[1], $date[2], $date[0])));
                $now=time(); //suan
                $i = 1;
                $gun_time = $first_day; // saat dakika saniye ay gün yıl
                while($gun_time<$now)
                {
                    $other = date('Y-m-d H:i:s', mktime(0, 0, -1, $date[1], $date[2]+$i, $date[0]));
                    $gun = date('Y-m-d H:i:s',$gun_time);
                    if($servisid=="6"){
                        $sube = Sube::whereIn('netsiscari_id',$cariid)->where('aktif',1)->first();
                        if($sube)
                            $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->where('servis_id',$servisid)->where('subekodu',$sube->subekodu)->where('durum','<>',2)->sum('adet');
                        else
                            $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->where('servis_id',$servisid)->whereNotIn('depokodu',array(6,24,25))->where('durum','<>',2)->sum('adet');
                        if($sube){
                            $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)->where('subekodu',$sube->subekodu)
                                ->where(function($query){$query->whereNotNull('onaylanmatarihi')->orWhereNotNull('gerigonderimtarihi')
                                    ->orWhereNotNull('hurdalamatarihi');})->count();
                        }else{
                            $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)
                                ->where(function($query){$query->whereNotNull('onaylanmatarihi')->orWhereNotNull('gerigonderimtarihi')
                                    ->orWhereNotNull('hurdalamatarihi');})->count();
                        }
                    }else{
                        $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->whereIn('depokodu',array(6,24,25))->where('servis_id', $servisid)->where('durum','<>',2)->sum('adet');
                        $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)
                            ->where(function($query){$query->whereNotNull('depoteslimtarihi')->orWhereNotNull('gerigonderimtarihi')
                                ->orWhereNotNull('hurdalamatarihi');})->count();
                    }
                    array_push($list,array('deger'=>date('M d',strtotime($gun)),'bolum'=>'Gun','date'=>date('d-m',strtotime($gun)),'depogelen'=>$depogelen,'biten'=>$biten));
                    $gun_time = strtotime(date('Y-m-d H:i:s', mktime(0, 0, 0, $date[1], $date[2]+$i, $date[0])));
                    $i++;
                }
                break; // son 2 hafta
            case "5": //son 1 ay haftalara böl
                $date=explode('-',$date);
                $first_day = strtotime(date('Y-m-d H:i:s', mktime(0, 0, 0, $date[1], $date[2], $date[0])));
                $now=time(); //suan
                $i = 0;
                $gun_time = $first_day; // saat dakika saniye ay gün yıl
                $day = date('w', $first_day);
                while($gun_time<$now)
                {
                    $j=(8-$day)+(7*$i);
                    $other = date('Y-m-d H:i:s', mktime(0, 0, -1, $date[1], $date[2]+$j, $date[0]));
                    $gun = date('Y-m-d H:i:s',$gun_time);
                    if($servisid=="6"){
                        $sube = Sube::whereIn('netsiscari_id',$cariid)->where('aktif',1)->first();
                        if($sube)
                            $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->where('servis_id',$servisid)->where('subekodu',$sube->subekodu)->where('durum','<>',2)->sum('adet');
                        else
                            $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->where('servis_id',$servisid)->whereNotIn('depokodu',array(6,24,25))->where('durum','<>',2)->sum('adet');
                        if($sube){
                            $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)->where('subekodu',$sube->subekodu)
                                ->where(function($query){$query->whereNotNull('onaylanmatarihi')->orWhereNotNull('gerigonderimtarihi')
                                    ->orWhereNotNull('hurdalamatarihi');})->count();
                        }else{
                            $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)
                                ->where(function($query){$query->whereNotNull('onaylanmatarihi')->orWhereNotNull('gerigonderimtarihi')
                                    ->orWhereNotNull('hurdalamatarihi');})->count();
                        }
                    }else{
                        $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->whereIn('depokodu',array(6,24,25))->where('servis_id', $servisid)->where('durum','<>',2)->sum('adet');
                        $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)
                            ->where(function($query){$query->whereNotNull('depoteslimtarihi')->orWhereNotNull('gerigonderimtarihi')
                                ->orWhereNotNull('hurdalamatarihi');})->count();
                    }
                    array_push($list,array('deger'=>date('W',strtotime($gun)),'bolum'=>'Hafta','date'=>date('W-Y',strtotime($gun)),'depogelen'=>$depogelen,'biten'=>$biten));
                    $gun_time = strtotime(date('Y-m-d H:i:s', mktime(0, 0, 0, $date[1], $date[2]+$j, $date[0])));
                    $i++;
                }
                break; // son 1 ay
            case "6": // son 3 ay haftalara böl
                $date=explode('-',$date);
                $first_day = strtotime(date('Y-m-d H:i:s', mktime(0, 0, 0, $date[1], $date[2], $date[0])));
                $now=time(); //suan
                $i = 1;
                $gun_time = $first_day; // saat dakika saniye ay gün yıl
                $day = date('w', $first_day);
                while($gun_time<$now)
                {
                    $j=(8-$day)+(7*$i);
                    $other = date('Y-m-d H:i:s', mktime(0, 0, -1, $date[1], $date[2]+$j, $date[0]));
                    $gun = date('Y-m-d H:i:s',$gun_time);
                    if($servisid=="6"){
                        $sube = Sube::whereIn('netsiscari_id',$cariid)->where('aktif',1)->first();
                        if($sube)
                            $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->where('servis_id',$servisid)->where('subekodu',$sube->subekodu)->where('durum','<>',2)->sum('adet');
                        else
                            $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->where('servis_id',$servisid)->whereNotIn('depokodu',array(6,24,25))->where('durum','<>',2)->sum('adet');
                        if($sube){
                            $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)->where('subekodu',$sube->subekodu)
                                ->where(function($query){$query->whereNotNull('onaylanmatarihi')->orWhereNotNull('gerigonderimtarihi')
                                    ->orWhereNotNull('hurdalamatarihi');})->count();
                        }else{
                            $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)
                                ->where(function($query){$query->whereNotNull('onaylanmatarihi')->orWhereNotNull('gerigonderimtarihi')
                                    ->orWhereNotNull('hurdalamatarihi');})->count();
                        }
                    }else{
                        $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->whereIn('depokodu',array(6,24,25))->where('servis_id', $servisid)->where('durum','<>',2)->sum('adet');
                        $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)
                            ->where(function($query){$query->whereNotNull('depoteslimtarihi')->orWhereNotNull('gerigonderimtarihi')
                                ->orWhereNotNull('hurdalamatarihi');})->count();
                    }
                    array_push($list,array('deger'=>date('W',strtotime($gun)),'bolum'=>'Hafta','date'=>date('W-Y',strtotime($gun)),'depogelen'=>$depogelen,'biten'=>$biten));
                    $gun_time = strtotime(date('Y-m-d H:i:s', mktime(0, 0, 0, $date[1], $date[2]+$j, $date[0])));
                    $i++;
                }
                break; // son 3 ay
            case "7": // son 6 ay aylara böl
                $date=explode('-',$date);
                $first_day = strtotime(date('Y-m-d H:i:s', mktime(0, 0, 0, $date[1], $date[2], $date[0])));
                $now=time(); //suan
                $i = 1;
                $gun_time = $first_day; // saat dakika saniye ay gün yıl
                while($gun_time<$now)
                {
                    $other = date('Y-m-d H:i:s', mktime(0, 0, -1, $date[1]+$i, 1, $date[0]));
                    $gun = date('Y-m-d H:i:s',$gun_time);
                    if($servisid=="6"){
                        $sube = Sube::whereIn('netsiscari_id',$cariid)->where('aktif',1)->first();
                        if($sube)
                            $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->where('servis_id',$servisid)->where('subekodu',$sube->subekodu)->where('durum','<>',2)->sum('adet');
                        else
                            $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->where('servis_id',$servisid)->whereNotIn('depokodu',array(6,24,25))->where('durum','<>',2)->sum('adet');
                        if($sube){
                            $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)->where('subekodu',$sube->subekodu)
                                ->where(function($query){$query->whereNotNull('onaylanmatarihi')->orWhereNotNull('gerigonderimtarihi')
                                    ->orWhereNotNull('hurdalamatarihi');})->count();
                        }else{
                            $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)
                                ->where(function($query){$query->whereNotNull('onaylanmatarihi')->orWhereNotNull('gerigonderimtarihi')
                                    ->orWhereNotNull('hurdalamatarihi');})->count();
                        }
                    }else{
                        $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->whereIn('depokodu',array(6,24,25))->where('servis_id', $servisid)->where('durum','<>',2)->sum('adet');
                        $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)
                            ->where(function($query){$query->whereNotNull('depoteslimtarihi')->orWhereNotNull('gerigonderimtarihi')
                                ->orWhereNotNull('hurdalamatarihi');})->count();
                    }
                    array_push($list,array('deger'=>date('m',strtotime($gun)),'bolum'=>'Ay','date'=>date('m-Y',strtotime($gun)),'depogelen'=>$depogelen,'biten'=>$biten));
                    $gun_time = strtotime(date('Y-m-d H:i:s', mktime(0, 0, 0, $date[1]+$i, 1, $date[0])));
                    $i++;
                }
                break; // son 6 ay
            case "8": // son 1 yıl aylara böl
                $date=explode('-',$date);
                $first_day = strtotime(date('Y-m-d H:i:s', mktime(0, 0, 0, $date[1], $date[2], $date[0])));
                $now=time(); //suan
                $i = 1;
                $gun_time = $first_day; // saat dakika saniye ay gün yıl
                while($gun_time<$now)
                {
                    $other = date('Y-m-d H:i:s', mktime(0, 0, -1, $date[1]+$i, $date[2], $date[0]));
                    $gun = date('Y-m-d H:i:s',$gun_time);
                    if($servisid=="6"){
                        $sube = Sube::whereIn('netsiscari_id',$cariid)->where('aktif',1)->first();
                        if($sube)
                            $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->where('servis_id',$servisid)->where('subekodu',$sube->subekodu)->where('durum','<>',2)->sum('adet');
                        else
                            $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->where('servis_id',$servisid)->whereNotIn('depokodu',array(6,24,25))->where('durum','<>',2)->sum('adet');
                        if($sube){
                            $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)->where('subekodu',$sube->subekodu)
                                ->where(function($query){$query->whereNotNull('onaylanmatarihi')->orWhereNotNull('gerigonderimtarihi')
                                    ->orWhereNotNull('hurdalamatarihi');})->count();
                        }else{
                            $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)
                                ->where(function($query){$query->whereNotNull('onaylanmatarihi')->orWhereNotNull('gerigonderimtarihi')
                                    ->orWhereNotNull('hurdalamatarihi');})->count();
                        }
                    }else{
                        $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->whereIn('depokodu',array(6,24,25))->where('servis_id', $servisid)->where('durum','<>',2)->sum('adet');
                        $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)
                            ->where(function($query){$query->whereNotNull('depoteslimtarihi')->orWhereNotNull('gerigonderimtarihi')
                                ->orWhereNotNull('hurdalamatarihi');})->count();
                    }
                    array_push($list,array('deger'=>date('m',strtotime($gun)),'bolum'=>'Ay','date'=>date('m-Y',strtotime($gun)),'depogelen'=>$depogelen,'biten'=>$biten));
                    $gun_time = strtotime(date('Y-m-d H:i:s', mktime(0, 0, 0, $date[1]+$i, $date[2], $date[0])));
                    $i++;
                }
                break; // son 1 yıl
            case "9": // tamamı 2014 ten başlayacağız yıllara böl
                $date=2014;
                $first_day = strtotime(date('Y-m-d H:i:s', mktime(0, 0, 0, 1, 1, $date)));
                $now=time(); //suan
                $i = 1;
                $gun_time = $first_day; // saat dakika saniye ay gün yıl
                while($gun_time<$now)
                {
                    $other = date('Y-m-d H:i:s', mktime(0, 0, -1, 1, 1, $date+$i));
                    $gun = date('Y-m-d H:i:s',$gun_time);
                    if($servisid=="6"){
                        $sube = Sube::whereIn('netsiscari_id',$cariid)->where('aktif',1)->first();
                        if($sube)
                            $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->where('servis_id',$servisid)->where('subekodu',$sube->subekodu)->where('durum','<>',2)->sum('adet');
                        else
                            $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->where('servis_id',$servisid)->whereNotIn('depokodu',array(6,24,25))->where('durum','<>',2)->sum('adet');
                        if($sube){
                            $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)->where('subekodu',$sube->subekodu)
                                ->where(function($query){$query->whereNotNull('onaylanmatarihi')->orWhereNotNull('gerigonderimtarihi')
                                    ->orWhereNotNull('hurdalamatarihi');})->count();
                        }else{
                            $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)
                                ->where(function($query){$query->whereNotNull('onaylanmatarihi')->orWhereNotNull('gerigonderimtarihi')
                                    ->orWhereNotNull('hurdalamatarihi');})->count();
                        }
                    }else{
                        $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->whereIn('depokodu',array(6,24,25))->where('servis_id', $servisid)->where('durum','<>',2)->sum('adet');
                        $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)
                            ->where(function($query){$query->whereNotNull('depoteslimtarihi')->orWhereNotNull('gerigonderimtarihi')
                                ->orWhereNotNull('hurdalamatarihi');})->count();
                    }
                    array_push($list,array('deger'=>date('Y',strtotime($gun)),'bolum'=>'Yıl','date'=>date('Y',strtotime($gun)),'depogelen'=>$depogelen,'biten'=>$biten));
                    $gun_time = strtotime(date('Y-m-d H:i:s', mktime(0, 0, 0, 1, 1, $date+$i)));
                    $i++;
                }
                break; // tamamı
            default:
                $date=2014;
                $first_day = strtotime(date('Y-m-d H:i:s', mktime(0, 0, 0, 1, 1, $date)));
                $now=time(); //suan
                $i = 1;
                $gun_time = $first_day; // saat dakika saniye ay gün yıl
                while($gun_time<$now)
                {
                    $other = date('Y-m-d H:i:s', mktime(0, 0, -1, 1, 1, $date+$i));
                    $gun = date('Y-m-d H:i:s',$gun_time);
                    if($servisid=="6"){
                        $sube = Sube::whereIn('netsiscari_id',$cariid)->where('aktif',1)->first();
                        if($sube)
                            $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->where('servis_id',$servisid)->where('subekodu',$sube->subekodu)->where('durum','<>',2)->sum('adet');
                        else
                            $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->where('servis_id',$servisid)->whereNotIn('depokodu',array(6,24,25))->where('durum','<>',2)->sum('adet');
                        if($sube){
                            $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)->where('subekodu',$sube->subekodu)
                                ->where(function($query){$query->whereNotNull('onaylanmatarihi')->orWhereNotNull('gerigonderimtarihi')
                                    ->orWhereNotNull('hurdalamatarihi');})->count();
                        }else{
                            $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)
                                ->where(function($query){$query->whereNotNull('onaylanmatarihi')->orWhereNotNull('gerigonderimtarihi')
                                    ->orWhereNotNull('hurdalamatarihi');})->count();
                        }
                    }else{
                        $depogelen = DepoGelen::whereBetween('tarih',array($gun,$other))->whereIn('depokodu',array(6,24,25))->where('servis_id', $servisid)->where('durum','<>',2)->sum('adet');
                        $biten = ServisTakip::whereBetween('depotarih',array($gun,$other))->where('servis_id', $servisid)
                            ->where(function($query){$query->whereNotNull('depoteslimtarihi')->orWhereNotNull('gerigonderimtarihi')
                                ->orWhereNotNull('hurdalamatarihi');})->count();
                    }
                    array_push($list,array('deger'=>date('Y',strtotime($gun)),'bolum'=>'Yıl','date'=>date('Y',strtotime($gun)),'depogelen'=>$depogelen,'biten'=>$biten));
                    $gun_time = strtotime(date('Y-m-d H:i:s', mktime(0, 0, 0, 1, 1, $date+$i)));
                    $i++;
                }
                break; // tamamı
        }
        return $list;
    }

    public static function getGelenBekleyen($date,$servisid,$toplamgelen,$cariid=array()){
        $adet=array(0,0,0,0,0,0,0,0,0,0,0,0,0);
        if($servisid=="6"){
            $sube = Sube::whereIn('netsiscari_id',$cariid)->where('aktif',1)->first();
            if($sube){
                $servistakip=ServisTakip::where('depotarih','>=',$date)->where('servis_id',$servisid)->where('subekodu',$sube->subekodu)->get();
            }else{
                $servistakip=ServisTakip::where('depotarih','>=',$date)->where('servis_id',$servisid)->get();
            }
            foreach($servistakip as $takip) {
                if($sube){
                    if($takip->durum>11)
                        $adet[12]++;
                    else
                        $adet[($takip->durum)]++;
                    $toplamgelen--;
                }else{
                    if($takip->durum>11)
                        $adet[12]++;
                    else
                        $adet[($takip->durum)]++;
                    $toplamgelen--;
                }
            }
            $adet[0]=$toplamgelen;
        }else{
            $servistakip=ServisTakip::where('depotarih','>=',$date)->where('servis_id',$servisid)->get();
            foreach($servistakip as $takip) {
                if($takip->durum>11)
                    $adet[12]++;
                else
                    $adet[($takip->durum)]++;
                $toplamgelen--;
            }
            $adet[0]=$toplamgelen;
        }

        return $adet;
    }

    public static function getDepoGelenSayi($date,$servisid,$cariid=array()){
        switch ($servisid) {
            case 1: case 2: case 3: case 4:case 5: $depogelen=DepoGelen::where('tarih','>=',$date)->whereIn('depokodu',array(6,24,25))->where('servis_id',$servisid)->where('durum','<>',2)->sum('adet');  break;
            case 6:
                $sube=Sube::whereIn('netsiscari_id',$cariid)->where('aktif',1)->first();
                if($sube)
                    $depogelen=DepoGelen::where('tarih','>=',$date)->where('servis_id',6)->where('subekodu',$sube->subekodu)->where('durum','<>',2)->sum('adet');
                else
                    $depogelen=DepoGelen::where('tarih','>=',$date)->where('servis_id',6)->whereNotIn('depokodu',array(6,24,25))->where('durum','<>',2)->sum('adet');
                break;
            default:$depogelen = DepoGelen::find(0)->sum('adet');
        }
        return $depogelen;
    }

    public static function getChartbilgi(){
        $tarih=Input::get('tarih');
        $servisler=Servis::where('id','<>',0)->get();
        $data=array();
        switch ($tarih) {
            case "1": $date = date("Y-m-d", strtotime("first day of january this year"));  break; // bu yıl
            case "2": $date = date("Y-m-d", time());                break; // bugün
            case "3": $date = date("Y-m-d", strtotime("-1 week")); break; // son 1 hafta
            case "4": $date = date("Y-m-d", strtotime("-2 weeks"));  break; // son 2 hafta
            case "5": $date = date("Y-m-d", strtotime("-1 month"));  break; // son 1 ay
            case "6": $date = date("Y-m-d", strtotime("-3 months"));  break; // son 3 ay
            case "7": $date = date("Y-m-d", strtotime("-6 months")); break; // son 6 ay
            case "8": $date = date("Y-m-d", strtotime("-1 year"));  break; // son 1 yıl
            case "9": $date = date("Y-m-d", strtotime(""));  break; // tamamı
            default:$date = date("Y-m-d", strtotime(""));         break; // tamamı
        }

        foreach($servisler as $servis){
            // sayackayit,arizakayit,ucretlendirme,formgonderim,onaylama,reddetme,tekrarucret,kalibrasyon,depoteslim,gerigonderim,hurdalama
            if($servis->id=="6"){
                $toplamgelen=MainController::getDepoGelenSayi($date,$servis->id,Auth::user()->netsiscari_id);
                $gelenbiten=MainController::getGelenBiten($date,$servis->id,$tarih,Auth::user()->netsiscari_id);
                $kalanlar= MainController::getGelenBekleyen($date,$servis->id,$toplamgelen,Auth::user()->netsiscari_id);
                $toplambiten=$kalanlar[5]+$kalanlar[6]+$kalanlar[7]+$kalanlar[8]+$kalanlar[9]+$kalanlar[10]+$kalanlar[11];
                if($toplamgelen==0){
                    $oran=0;
                }else{
                    $oran=round((($toplambiten/$toplamgelen)*100),2);
                }
            }else{
                $toplamgelen=MainController::getDepoGelenSayi($date,$servis->id);
                $gelenbiten=MainController::getGelenBiten($date,$servis->id,$tarih);
                $kalanlar= MainController::getGelenBekleyen($date,$servis->id,$toplamgelen);
                $toplambiten=$kalanlar[9]+$kalanlar[10]+$kalanlar[11];
                if($toplamgelen==0){
                    $oran=0;
                }else{
                    $oran=round((($toplambiten/$toplamgelen)*100),2);
                }
            }
            array_push($data,array('toplamgelen'=>$toplamgelen,'toplambiten'=>$toplambiten,'oran'=>$oran,'gelenbiten'=>$gelenbiten,'kalanlar'=>$kalanlar));
        }
        return Response::json(array('servisid'=>Auth::user()->servis_id,'data' => $data));
    }

}
