<?php

use Carbon\Carbon;
use Mtownsend\XmlToArray\XmlToArray;

class BackendController extends BaseController {

	public function _construct(){
        $this->beforeFilter('csrf',array('on'=>array('post','delete','put')));
        $this->beforeFilter('ajax',array('on'=>array('delete','put')));
    }

    public static function time_elapsed($ptime){
        $date = strtotime($ptime);
        $etime = time() - $date;
        /*if ($etime < 86400)
        {
            return 'Bugün';
        }*/

        $a = array( 365 * 24 * 60 * 60  =>  'yıl',    //946080000
            30 * 24 * 60 * 60  =>  'ay',     //2592000
            24 * 60 * 60  =>  'gun',    //86400
            60 * 60  =>  'saat',   //3600
            60  =>  'dakika', //60
            1  =>  'saniye'  //1
        );
        $a_plural = array( 'yıl'    => 'yıl',
            'ay'     => 'ay',
            'gun'    => 'gun',
            'saat'   => 'saat',
            'dakika' => 'dakika',
            'saniye' => 'saniye'
        );

        foreach ($a as $secs => $str)
        {
            $d = $etime / $secs;
            if ($d >= 1)
            {
                $r = floor($d);
                return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' önce';
            }
        }
        return 'Şimdi';
    }

    public static function getHatirlatmadurum(){
       if( Auth::user()->grup_id <= "4" )     // yönetici
           return array(1,2,3,4,5,6);
       elseif( Auth::user()->grup_id == "5" ) // servis müdürü
           return array(1,2,3,4,5);
       elseif( Auth::user()->grup_id == "7" || Auth::user()->grup_id == "11" ) // su gaz ısı
           return array(1,3,4);
       elseif( Auth::user()->grup_id == "8" || Auth::user()->grup_id == "12") // elektrik
           return array(2);
       elseif( Auth::user()->grup_id == "9" || Auth::user()->grup_id == "13") // mekanik gaz
           return array(5);
       elseif( Auth::user()->grup_id == "16" )     // depo
           return array(1,2,3,4,5);
       elseif( Auth::user()->grup_id == "6" ) // şatış yetkilisi
           return array(1,2,3,4,5,6);
       elseif( Auth::user()->grup_id == "17" ) // şube yetkilisi
           return array(6);
       else                                 // kullanıcı
           return array(1,2,3,4,5,6);
    }

    public static function getHatirlatmatipdurum(){
        if( Auth::user()->grup_id == "6" ) // şatış yetkilisi ücretlendirme bekleyenleri görecek düzenleyebilecek
            return array(4,5,6,7);
        elseif( Auth::user()->grup_id == "17" ) // şube yetkilisi
            return array(2,3,4,5,6,7,9,11,12,13);
        elseif( Auth::user()->group_id == "18" ) // şube elemanı
            return array(13);
        elseif( Auth::user()->grup_id == "19" ) // kullanıcı sadece müsteri onayı bekleyenleri görecek
            return array(6);
        else                                 // diğer
            return array(2,3,4,5,6,7,8,9,11,12,13);
    }

    public static function getBildirimtipdurum(){
        if( Auth::user()->grup_id == "6" ) // satış yetkilisi ücretlendirme bitenleri görecek düzenleyebilecek
            return array(4,5,6,7);
        elseif( Auth::user()->grup_id == "17" ) // şube yetkilisi
            return array(2,3,4,5,6,7,9,10,11,12,13);
        elseif( Auth::user()->group_id == "18" ) // şube elemanı
            return array(13);
        elseif( Auth::user()->grup_id == "19" ) // kullanıcı cari adına ait ne varsa görebilecek
            return array(6,7);
        else                                 // diğer
            return array(1,2,3,4,5,6,7,8,9,10,11,12,13);
    }

    public static function getOnaybildirimtipdurum(){
        if(Auth::user()->grup_id == "1" || Auth::user()->grup_id == "2" || Auth::user()->grup_id == "3" || Auth::user()->grup_id == "4" || Auth::user()->grup_id == "5")
            return array(6,7);
	    else if(Auth::user()->grup_id == "6" ) // satış yetkilisi ücretlendirme bitenleri görecek düzenleyebilecek
            return array(6,7);
        elseif( Auth::user()->grup_id == "17" ) // şube yetkilisi
            return array(6,7);
        elseif( Auth::user()->grup_id == "19" ) // kullanıcı cari adına ait ne varsa görebilecek
            return array(6,7);
        else                                 // diğer
            return array(-1);
    }

    public static function getGenelParabirimi(){
        $uretimyer = UretimYer::find(0);
        return ParaBirimi::find($uretimyer->parabirimi_id);
    }

    public static function getOzelParabirimi($id){
        $uretimyer = UretimYer::find($id);
        return ParaBirimi::find($uretimyer->parabirimi_id);
    }

    public static function getYerparabirimi(){
        try {
            $id=Input::get('id');
            $uretimyeri = UretimYer::find($id);
            $parabirimi = $uretimyeri->parabirimi->birimi;
            return Response::json(array('durum'=>true,'parabirimi' => $parabirimi));
        } catch (Exception $e) {
            return Response::json(array('durum'=>false,'title'=>'Para Birimi Hatalı','text'=>'Bu Yere Ait Para Birimi Hatalı','type'=>'error'));
        }
    }

    public static function getHatirlatma($hatirlatmadurum,$tipdurum,$netsiscari_id){
        if(count($netsiscari_id)>0)
            $hatirlatma = Hatirlatma::with('depogelen','servis','netsiscari')->whereIn('servis_id',$hatirlatmadurum)->whereIn('hatirlatmatip_id',$tipdurum)->whereIn('netsiscari_id',$netsiscari_id)->where('tip',2)->where('durum',0)->orderBy('tarih','desc')->orderBy('id','desc')->take(5)->get();
        else
            $hatirlatma = Hatirlatma::with('depogelen','servis','netsiscari')->whereIn('servis_id',$hatirlatmadurum)->whereIn('hatirlatmatip_id',$tipdurum)->where('tip',2)->where('durum',0)->orderBy('tarih','desc')->orderBy('id','desc')->take(5)->get();
        $root = BackendController::getRootDizin();
        foreach($hatirlatma as $hatirlatma_row)
        {
            $servisadi="";
            switch($hatirlatma_row->servis_id){
                case 1: $servisadi="suservis";
                    $hatirlatma_row->label = "label-info";
                    break;
                case 2: $servisadi="elkservis";
                    $hatirlatma_row->label = "label-danger";
                    break;
                case 3: $servisadi="gazservis";
                    $hatirlatma_row->label = "label-warning";
                    break;
                case 4: $servisadi="isiservis";
                    $hatirlatma_row->label = "label-success";
                    break;
                case 5: $servisadi="mekanikgaz";
                    $hatirlatma_row->label = "label-warning";
                    break;
                case 6: $servisadi="sube";
                    $hatirlatma_row->label = "label-primary";
                    break;
            }
            if ($hatirlatma_row->hatirlatmatip_id == 2) { //Sayaç Kayıt Bekliyor
                $hatirlatma_row->link =  $root."/".$servisadi."/sayackayitekle/".$hatirlatma_row->id;
                $hatirlatma_row->icon = "fa-thumbs-o-up";
                $hatirlatma_row->time = BackendController::time_elapsed($hatirlatma_row->tarih);
                $hatirlatma_row->notify = "Sayaçların Sisteme Girilmesi Bekleniyor: ".$hatirlatma_row->adet." Adet ".$hatirlatma_row->servis->servisadi.
                    "<br>". $hatirlatma_row->netsiscari->cariadi.". Kalan: ".$hatirlatma_row->kalan." Adet";
            } else if ($hatirlatma_row->hatirlatmatip_id == 3) { //Arıza Kayıt Bekliyor
                $hatirlatma_row->link =  $root."/".$servisadi."/arizakayitekle/".$hatirlatma_row->id;
                $hatirlatma_row->icon = "fa-pencil";
                $hatirlatma_row->time = BackendController::time_elapsed($hatirlatma_row->tarih);
                $hatirlatma_row->notify = "Sayaçların Arıza Kayıtları Girilmesi Bekleniyor: ".$hatirlatma_row->adet." Adet ".$hatirlatma_row->servis->servisadi.
                    "<br>". $hatirlatma_row->netsiscari->cariadi.". Kalan: ".$hatirlatma_row->kalan." Adet";
            } else if ($hatirlatma_row->hatirlatmatip_id == 4) { //Fiyatlandırma Bekliyor
                $hatirlatma_row->link = $root."/ucretlendirme/ucretlendirmekayit/".$hatirlatma_row->id;
                $hatirlatma_row->icon = "fa-check";
                $hatirlatma_row->time = BackendController::time_elapsed($hatirlatma_row->tarih);
                $hatirlatma_row->notify = "Sayaçların Arıza Tamir Fiyatlandırması Bekleniyor: ".$hatirlatma_row->adet." Adet ".$hatirlatma_row->servis->servisadi.
                    "<br>". $hatirlatma_row->netsiscari->cariadi.". Kalan: ".$hatirlatma_row->kalan." Adet";
            } else if ($hatirlatma_row->hatirlatmatip_id == 5) { //Form Gönderimi Bekliyor
                $hatirlatma_row->link = $root."/ucretlendirme/ucretlendirilenler/".$hatirlatma_row->id;
                $hatirlatma_row->icon = "fa-envelope-o";
                $hatirlatma_row->time = BackendController::time_elapsed($hatirlatma_row->tarih);
                $hatirlatma_row->notify = "Onaylamanın Düzenlenmesi Bekleniyor: ".$hatirlatma_row->adet." Adet ".$hatirlatma_row->servis->servisadi.
                    "<br>". $hatirlatma_row->netsiscari->cariadi.". Kalan: ".$hatirlatma_row->kalan." Adet";
            } else if ($hatirlatma_row->hatirlatmatip_id == 6) { //Müşteri Onayı Bekliyor
                if(Auth::user()->grup_id==19) //kullanıcı ise
                    $hatirlatma_row->link = $root."/abone/musterionay/".$hatirlatma_row->id;
                else
                    $hatirlatma_row->link = "#";
                $hatirlatma_row->icon = "fa-user";
                $hatirlatma_row->time = BackendController::time_elapsed($hatirlatma_row->tarih);
                $hatirlatma_row->notify = "Sayaç Tamir Fiyatı için Onay Bekleniyor: ".$hatirlatma_row->adet." Adet ".$hatirlatma_row->servis->servisadi.
                    "<br>". $hatirlatma_row->netsiscari->cariadi.". Kalan: ".$hatirlatma_row->kalan." Adet";
            } else if ($hatirlatma_row->hatirlatmatip_id == 7) { //Tekrar Fiyatlandırma Bekliyor
                $hatirlatma_row->link = $root."/ucretlendirme/ucretlendirmekayit/".$hatirlatma_row->id;
                $hatirlatma_row->icon = "fa-minus-square-o";
                $hatirlatma_row->time = BackendController::time_elapsed($hatirlatma_row->tarih);
                $hatirlatma_row->notify = "Tekrar Fiyatlandırma Bekleniyor: ".$hatirlatma_row->adet." Adet ".$hatirlatma_row->servis->servisadi.
                    "<br>". $hatirlatma_row->netsiscari->cariadi.". Kalan: ".$hatirlatma_row->kalan." Adet";
            } else if ($hatirlatma_row->hatirlatmatip_id == 8) { //Kalibrasyon Bekliyor
                $hatirlatma_row->link = $root."/kalibrasyon/kalibrasyon/".$hatirlatma_row->id;
                $hatirlatma_row->icon = "fa-cog";
                $hatirlatma_row->time = BackendController::time_elapsed($hatirlatma_row->tarih);
                $hatirlatma_row->notify = "Kalibrasyon Bekleniyor: ".$hatirlatma_row->adet." Adet ".$hatirlatma_row->servis->servisadi.
                    "<br>". $hatirlatma_row->netsiscari->cariadi.". Kalan: ".$hatirlatma_row->kalan." Adet";
            } else if ($hatirlatma_row->hatirlatmatip_id == 9){ //Depo Teslimi Bekliyor
                $hatirlatma_row->link = $root."/depo/depoteslim/".$hatirlatma_row->id;
                $hatirlatma_row->icon = "fa-truck";
                $hatirlatma_row->time = BackendController::time_elapsed($hatirlatma_row->tarih);
                $hatirlatma_row->notify = "Sayaçların Depoya Teslim Edilmesi Bekleniyor: ".$hatirlatma_row->adet." Adet ".$hatirlatma_row->servis->servisadi.
                    "<br>". $hatirlatma_row->netsiscari->cariadi.". Kalan: ".$hatirlatma_row->kalan." Adet";
            } else if ($hatirlatma_row->hatirlatmatip_id == 11){ //Depolararası Bekliyor
                $hatirlatma_row->link = $root."/depo/depolararasi/".$hatirlatma_row->id;
                $hatirlatma_row->icon = "fa-truck";
                $hatirlatma_row->time = BackendController::time_elapsed($hatirlatma_row->tarih);
                $hatirlatma_row->notify = "Depolararası Transfer Bekleniyor: ".$hatirlatma_row->adet." Adet ".$hatirlatma_row->servis->servisadi.
                    "<br>". $hatirlatma_row->netsiscari->cariadi.". Kalan: ".$hatirlatma_row->kalan." Adet";
            } else if ($hatirlatma_row->hatirlatmatip_id == 12){ //Abone Teslim Bekliyor
                $hatirlatma_row->link = $root."/depo/aboneteslim/".$hatirlatma_row->id;
                $hatirlatma_row->icon = "fa-user";
                $hatirlatma_row->time = BackendController::time_elapsed($hatirlatma_row->tarih);
                $hatirlatma_row->notify = "Aboneye Teslim Bekleniyor: ".$hatirlatma_row->adet." Adet ".$hatirlatma_row->servis->servisadi.
                    "<br>". $hatirlatma_row->netsiscari->cariadi.". Kalan: ".$hatirlatma_row->kalan." Adet";
            } else if ($hatirlatma_row->hatirlatmatip_id == 13){ //Servis Kayıdı Bekliyor
                $hatirlatma_row->link = $root."/sube/serviskayit/".$hatirlatma_row->id;
                $hatirlatma_row->icon = "fa-child";
                $hatirlatma_row->time = BackendController::time_elapsed($hatirlatma_row->tarih);
                $hatirlatma_row->notify = "Servis Kayıdı Bekleniyor: ".$hatirlatma_row->adet." Adet ".$hatirlatma_row->servis->servisadi.
                    "<br>". $hatirlatma_row->netsiscari->cariadi.". Kalan: ".$hatirlatma_row->kalan." Adet";
            }
        }
        return $hatirlatma;
    }

    public static function getBildirim($hatirlatmadurum,$tipdurum,$netsiscari_id){
        if(count($netsiscari_id)>0)
        {
            $bildirim = Hatirlatma::with('depogelen','servis','netsiscari')->whereIn('servis_id',$hatirlatmadurum)->whereIn('hatirlatmatip_id',$tipdurum)->whereIn('netsiscari_id',$netsiscari_id)->where('tip',1)->where('durum',0)->orderBy('tarih','desc')->orderBy('id','desc')->take(5)->get();
        }else {
            $bildirim = Hatirlatma::with('depogelen','servis','netsiscari')->whereIn('servis_id',$hatirlatmadurum)->whereIn('hatirlatmatip_id',$tipdurum)->where('tip',1)->where('durum',0)->orderBy('tarih','desc')->orderBy('id','desc')->take(5)->get();
        }
        $root = BackendController::getRootDizin();
        foreach($bildirim as $bildirim_row)
        {
            $servisadi="";
            switch($bildirim_row->servis_id){
                case 1: $servisadi="suservis";
                    $bildirim_row->label = "label-info";
                    break;
                case 2: $servisadi="elkservis";
                    $bildirim_row->label = "label-danger";
                    break;
                case 3: $servisadi="gazservis";
                    $bildirim_row->label = "label-warning";
                    break;
                case 4: $servisadi="isiservis";
                    $bildirim_row->label = "label-success";
                    break;
                case 5: $servisadi="mekanikgaz";
                    $bildirim_row->label = "label-warning";
                    break;
                case 6: $servisadi="sube";
                    $bildirim_row->label = "label-primary";
                    break;
            }
            if ($bildirim_row->hatirlatmatip_id == 1) { //Depo Kayıt Yapıldı
                $bildirim_row->link = $root."/depo/depogelen/".$bildirim_row->id;
                $bildirim_row->icon = "fa-thumbs-o-up";
                $bildirim_row->time = BackendController::time_elapsed($bildirim_row->tarih);
                $bildirim_row->notify = "Depo Girişi Yapıldı : ".$bildirim_row->adet." Adet ".$bildirim_row->servis->servisadi.
                    "<br>". $bildirim_row->netsiscari->cariadi;
            } else if ($bildirim_row->hatirlatmatip_id == 2) { //Sayaç Kayıt Yapıldı
                $bildirim_row->link = $root."/".$servisadi."/sayackayit/".$bildirim_row->id;
                $bildirim_row->icon = "fa-thumbs-o-up";
                $bildirim_row->time = BackendController::time_elapsed($bildirim_row->tarih);
                $bildirim_row->notify = "Sisteme Sayac Girişi Yapıldı : ".$bildirim_row->adet." Adet ".$bildirim_row->servis->servisadi.
                    "<br>". $bildirim_row->netsiscari->cariadi;
            } else if ($bildirim_row->hatirlatmatip_id == 3) { //Arıza Kayıt Yapıldı
                $bildirim_row->link = $root."/".$servisadi."/arizakayit/".$bildirim_row->id;
                $bildirim_row->icon = "fa-pencil";
                $bildirim_row->time = BackendController::time_elapsed($bildirim_row->tarih);
                $bildirim_row->notify = "Sayaç Arıza Kayıt Girişi Yapıldı : ".$bildirim_row->adet." Adet ".$bildirim_row->servis->servisadi.
                    "<br>". $bildirim_row->netsiscari->cariadi;
            } else if ($bildirim_row->hatirlatmatip_id == 4) { //Fiyatlandırma Yapıldı
                $bildirim_row->link = $root."/ucretlendirme/ucretlendirilenler/".$bildirim_row->id;
                $bildirim_row->icon = "fa-check";
                $bildirim_row->time = BackendController::time_elapsed($bildirim_row->tarih);
                $bildirim_row->notify = "Fiyatlandırma Yapıldı : ".$bildirim_row->adet." Adet ".$bildirim_row->servis->servisadi.
                    "<br>". $bildirim_row->netsiscari->cariadi;
            } else if ($bildirim_row->hatirlatmatip_id == 5) { //Onay Formu Gönderildi
                $bildirim_row->link = $root."/ucretlendirme/ucretlendirilenler/".$bildirim_row->id;
                $bildirim_row->icon = "fa-envelope-o";
                $bildirim_row->time = BackendController::time_elapsed($bildirim_row->tarih);
                $bildirim_row->notify = "Müşteriye Onay Formu Gönderildi : ".$bildirim_row->adet." Adet ".$bildirim_row->servis->servisadi.
                    "<br>". $bildirim_row->netsiscari->cariadi;
            } else if ($bildirim_row->hatirlatmatip_id == 6) { //Fiyatlandırma Reddedildi
                if(Auth::user()->grup_id==19) //kullanıcı ise
                    $bildirim_row->link = $root."/abone/musterionay/".$bildirim_row->id;
                else
                    $bildirim_row->link = $root."/ucretlendirme/reddedilenler/".$bildirim_row->id;
                $bildirim_row->icon = "fa-minus-square-o";
                $bildirim_row->time = BackendController::time_elapsed($bildirim_row->tarih);
                $bildirim_row->notify = "Fiyatlandırma Reddedildi : ".$bildirim_row->adet." Adet ".$bildirim_row->servis->servisadi.
                    "<br>". $bildirim_row->netsiscari->cariadi;
            } else if ($bildirim_row->hatirlatmatip_id == 7) { //Müşteri Onayı Alındı
                if(Auth::user()->grup_id==19) //kullanıcı ise
                    $bildirim_row->link = $root."/abone/musterionay/".$bildirim_row->id;
                else
                    $bildirim_row->link = $root."/ucretlendirme/onaylananlar/".$bildirim_row->id;
                $bildirim_row->icon = "fa-user";
                $bildirim_row->time = BackendController::time_elapsed($bildirim_row->tarih);
                $bildirim_row->notify = "Sayaç Tamiri Onaylandı : ".$bildirim_row->adet." Adet ".$bildirim_row->servis->servisadi.
                    "<br>". $bildirim_row->netsiscari->cariadi;
            } else if ($bildirim_row->hatirlatmatip_id == 8) { //Kalibrasyon Yapıldı
                $bildirim_row->link = $root."/kalibrasyon/kalibrasyon/".$bildirim_row->id;
                $bildirim_row->icon = "fa-cog";
                $bildirim_row->time = BackendController::time_elapsed($bildirim_row->tarih);
                $bildirim_row->notify = "Sayacın Kalibrasyonu Yapıldı : ".$bildirim_row->adet." Adet ".$bildirim_row->servis->servisadi.
                    "<br>". $bildirim_row->netsiscari->cariadi;
            } else if($bildirim_row->hatirlatmatip_id == 9) { //Depo Teslimi Yapıldı
                $bildirim_row->link = $root."/depo/depoteslim/".$bildirim_row->id;
                $bildirim_row->icon = "fa-truck";
                $bildirim_row->time = BackendController::time_elapsed($bildirim_row->tarih);
                $bildirim_row->notify = "Sayaçlar Depoya Teslim Edildi : ".$bildirim_row->adet." Adet ".$bildirim_row->servis->servisadi.
                    "<br>". $bildirim_row->netsiscari->cariadi;
            } else if($bildirim_row->hatirlatmatip_id == 10) { //Hurdaya Ayırıldı
                $bildirim_row->link = $root."/depo/hurda/".$bildirim_row->id;
                $bildirim_row->icon = "fa-truck";
                $bildirim_row->time = BackendController::time_elapsed($bildirim_row->tarih);
                $bildirim_row->notify = "Sayaçlar Hurdaya Ayrıldı : ".$bildirim_row->adet." Adet ".$bildirim_row->servis->servisadi.
                    "<br>". $bildirim_row->netsiscari->cariadi;
            } else if($bildirim_row->hatirlatmatip_id == 11) { //Depolararası Gönderildi
                $bildirim_row->link = $root."/depo/depolararasi/".$bildirim_row->id;
                $bildirim_row->icon = "fa-truck";
                $bildirim_row->time = BackendController::time_elapsed($bildirim_row->tarih);
                $bildirim_row->notify = "Sayaçlar Depolararası Gönderildi : ".$bildirim_row->adet." Adet ".$bildirim_row->servis->servisadi.
                    "<br>". $bildirim_row->netsiscari->cariadi;
            }else if($bildirim_row->hatirlatmatip_id == 12) { //Abone Teslim
                $bildirim_row->link = $root."/depo/aboneteslim/".$bildirim_row->id;
                $bildirim_row->icon = "fa-user";
                $bildirim_row->time = BackendController::time_elapsed($bildirim_row->tarih);
                $bildirim_row->notify = "Sayaçlar Aboneye Teslim Edildi : ".$bildirim_row->adet." Adet ".$bildirim_row->servis->servisadi.
                    "<br>". $bildirim_row->netsiscari->cariadi;
            }else if($bildirim_row->hatirlatmatip_id == 13) { //Servis Kayıdı
                $bildirim_row->link = $root."/sube/serviskayit/".$bildirim_row->id;
                $bildirim_row->icon = "fa-child";
                $bildirim_row->time = BackendController::time_elapsed($bildirim_row->tarih);
                $bildirim_row->notify = "Servis Kayıdı Açıldı : ".$bildirim_row->adet." Adet ".$bildirim_row->servis->servisadi.
                    "<br>". $bildirim_row->netsiscari->cariadi;
            }
        }
        return $bildirim;
    }

    public static function getHatirlatmaSayi($hatirlatmadurum,$tipdurum,$netsiscari_id)
    {
        if(count($netsiscari_id)>0)
        {
            return Hatirlatma::whereIn('servis_id', $hatirlatmadurum)->whereIn('hatirlatmatip_id',$tipdurum)->whereIn('netsiscari_id', $netsiscari_id)->where('tip',2)->where('durum', 0)->count();
        } else {
            return Hatirlatma::whereIn('servis_id', $hatirlatmadurum)->whereIn('hatirlatmatip_id',$tipdurum)->where('tip',2)->where('durum', 0)->count();
        }
    }

    public static function getBildirimSayi($hatirlatmadurum,$tipdurum,$netsiscari_id){
        if(count($netsiscari_id)>0)
        {
            return Hatirlatma::whereIn('servis_id', $hatirlatmadurum)->whereIn('hatirlatmatip_id',$tipdurum)->whereIn('netsiscari_id', $netsiscari_id)->where('tip',1)->where('durum', 0)->count();
        } else {
            return Hatirlatma::whereIn('servis_id', $hatirlatmadurum)->whereIn('hatirlatmatip_id',$tipdurum)->where('tip',1)->where('durum', 0)->count();
        }
    }

    public static function getHatirlatmaSayac($hatirlatmatip_id,$serino)//Hatirlatmaya ait kalan var ise 1 döndürür
    {
        $hatirlatmalar = Hatirlatma::select(array('hatirlatma.id', 'hatirlatma.kalan', 'hatirlatma.durum'))
            ->leftjoin('depogelen', 'hatirlatma.depogelen_id', '=', 'depogelen.id')
            ->leftjoin('sayacgelen', 'depogelen.id', '=', 'sayacgelen.depogelen_id')
            ->where('hatirlatma.hatirlatmatip_id', $hatirlatmatip_id)->where('sayacgelen.serino', $serino)->where('sayacgelen.arizakayit',0)->where('hatirlatma.tip', 2)->get();
        if ($hatirlatmalar->count()>0) {
            foreach ($hatirlatmalar as $hatirlatma) {
                if ($hatirlatma->durum == 0) {
                    if ($hatirlatma->kalan > 0)
                        return 1;// sayaca ait hatirlatma var
                }
            }
            return 2;
        } else {
            return 0;
        }
    }

    public static function getKayitDurum($serino,$servis_id){
        $sayacgelen = SayacGelen::where('serino',$serino)->where('servis_id',$servis_id)->where('arizakayit',0)->count();
        if ($sayacgelen>1) {
            return 2;
        }elseif($sayacgelen==1){
            return 1;
        }else{
            return 0;
        }
    }

    public static function getDepoKayitDurum($depogelenid){
        $sayacgelen = SayacGelen::where('depogelen_id',$depogelenid)->where('arizakayit',1)->count();
        if ($sayacgelen>0) {
            return 0;
        }else{
            return 1;
        }
    }

    public static function getArizaKayitDurum($depogelenid){
        $sayacgelen = SayacGelen::where('depogelen_id',$depogelenid)->where('arizakayit',0)->count();
        if ($sayacgelen>0) {
            return 0;
        }else{
            return 1;
        }
    }

    public static function getHatirlatmaId($hatirlatmatip_id,$servis_id,$depogelen_id,$netsiscari_id){//Hatirlatmaya id döndürür
        $hatirlatma = Hatirlatma::where('hatirlatmatip_id',$hatirlatmatip_id)->where('servis_id',$servis_id)
            ->where('depogelen_id',$depogelen_id)->where('tip',2)->where('netsiscari_id',$netsiscari_id)
            ->where('durum',0)->first();
        if($hatirlatma){
           return $hatirlatma->id;
        }else{
            return 0;
        }
    }

    public static function getGarantiDurum($gelistarihi,$oncekitarih,$garanti){
        $gelis = strtotime($gelistarihi);
        $onceki = strtotime($oncekitarih);
        $fark = $gelis - $onceki;
        $yil= 31536000; // 1 yıl = 31556926 saniye cinsinden
        $d = $fark / $yil;

        if($d>=$garanti)
        {
            return 0 ; //garanti dışı
        }else{
            return 1 ; //garanti içi
        }
    }

    public static function getKurBilgisi($parabirimiid,$kurtarih){
        if($parabirimiid==1)
            return 1;
        if(is_null($kurtarih))
            $doviz = DovizKuru::where('parabirimi_id',$parabirimiid)->orderBy('tarih','desc')->first();
        else
            $doviz = DovizKuru::where('parabirimi_id',$parabirimiid)->where('tarih',$kurtarih)->first();
        return $doviz->kurfiyati;
    }

    public static function getDovizkurgetir()
    {
        try {
            $tarih=date('Y-m-d',strtotime(Input::get('tarih')));
            if (is_null($tarih))
                $dovizkuru = DovizKuru::orderBy('tarih', 'desc')->orderBy('parabirimi_id', 'asc')->take(3)->get();
            else
                $dovizkuru = DovizKuru::where('tarih', $tarih)->orderBy('tarih', 'desc')->orderBy('parabirimi_id', 'asc')->take(3)->get();
            foreach ($dovizkuru as $doviz) {
                $doviz->tarih = date("d-m-Y", strtotime($doviz->tarih));
            }
            return Response::json(array('durum' => true, 'dovizkuru' => $dovizkuru));
        } catch (Exception $e) {
            Log::error($e);
            return Response::json(array('durum' => false, 'title' => 'Döviz Kurunda Hata Var!', 'text' => str_replace("'", "\'", $e->getMessage()), 'type' => 'warning'));
        }
    }

    public static function searchArray($secilenler,$list){
        foreach($secilenler as $secilen)
            foreach($list as $eleman)
            {
                if($eleman==$secilen)
                    return 1;
            }
        return 0;
    }

    public static function getUcretlendirmeFiyat($secilenler,$kurtarih,$birim,$birim2){
        $fiyat = 0.00;
        $fiyat2 = 0.00;
        $yenibirim=null;
        $secilenlist = explode(',',$secilenler);
        $arizafiyatlar = ArizaFiyat::whereIn('id',$secilenlist)->get();
        foreach($arizafiyatlar as $arizafiyat)
        {
            $kur = 1;
            if ($birim != $arizafiyat->parabirimi_id) {
                if ($birim == 1) { //tl
                    $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih);
                } else { //euro dolar sterln
                    if ($arizafiyat->parabirimi_id == 1) {
                        $kur = 1 / BackendController::getKurBilgisi($birim, $kurtarih);
                    } else {
                        $kur = BackendController::getKurBilgisi($arizafiyat->parabirimi_id, $kurtarih) / BackendController::getKurBilgisi($birim, $kurtarih);
                    }
                }
            }
            $arizafiyat->fiyat = $arizafiyat->fiyat * $kur;
            if($birim2==null){ //eski kayıtların 2. birimi kullanılacak
                if($arizafiyat->parabirimi2_id!=null){
                    if($arizafiyat->parabirimi2_id==$birim){
                        $arizafiyat->fiyat +=$arizafiyat->fiyat2;
                        $arizafiyat->fiyat2=0;
                        $arizafiyat->parabirimi2_id=null;
                    }else if($yenibirim==null){
                        $yenibirim=$arizafiyat->parabirimi2_id;
                    }else{
                        if($yenibirim!=$arizafiyat->parabirimi2_id){
                            return array('durum'=>true,'error'=>'İki Para Birimiden Fazla Kullanılamaz!');
                        }
                    }
                }
            }else{ // eski kayıtların uyup uymadığına bakılacak
                if($arizafiyat->parabirimi2_id!=null){
                    if($arizafiyat->parabirimi2_id==$birim){
                        $arizafiyat->fiyat +=$arizafiyat->fiyat2;
                        $arizafiyat->fiyat2=0;
                        $arizafiyat->parabirimi2_id=null;
                    }else if($arizafiyat->parabirimi2_id!=$birim2){
                        return array('durum'=>true,'error'=>'İki Para Birimiden Fazla Kullanılamaz!');
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
            $arizafiyat->parabirimi_id = $birim;
            $arizafiyat->save();

            $fiyat += $arizafiyat->toplamtutar;
            $fiyat2 += $arizafiyat->toplamtutar2;
        }
        return array('durum'=>false,'fiyat'=>$fiyat,'fiyat2'=>$fiyat2,'yenibirim'=>$yenibirim);
    }

    public static function getStokEslesme($degisen) {//Stok Eşleşme Durumunu döndürür
        if($degisen!="")
        {
            $degisenler=explode(',', $degisen);
            $stokdurum = StokDurum::whereIn('degisenler_id',$degisenler)->get();
            if($stokdurum->count() == count($degisenler) || $stokdurum->count()==0)
                return 1;
            else{
                $count=$stokdurum->count();
                $parcalar = Degisenler::whereIn('id',$degisenler)->where('parcadurum',1)->get();
                $count+=$parcalar->count();
                if($count== count($degisenler))
                    return 1;
                else {
                    $stokkontrol = Degisenler::whereIn('id',$degisenler)->where('parcadurum',0)->where('stokkontrol',0)->get();
                    $count+=$stokkontrol->count();
                    if($count== count($degisenler))
                        return 1;
                    else
                        return 0;
                }
            }
        }else{
            return 1;
        }
    }

    public static function getStokKodDurum() {//Stok Durumun Aktif ya da pasif olduğunu döndürür
        $stokdurum = SistemSabitleri::where('adi','StokDurum')->first();
        return $stokdurum->deger;
    }

    public static function getRootDizin($durum=false) { //durum false -> kendi root dizini , true-> dış ip
        //$rootdizin = SistemSabitleri::where('adi','RootDizin')->first(array('deger'));
        if($durum==false)
            return URL::to('/');
        else{
            $ipadresi= SistemSabitleri::where('adi','ServerDis')->first();
            //if($rootdizin && $rootdizin!="")
            //    return (!empty($_SERVER['HTTPS']) ? "http" : "http") . "://" . $ipadresi->deger ."/". $rootdizin->deger;
            //else if($rootdizin=="")
            //    return (!empty($_SERVER['HTTPS']) ? "http" : "http") . "://" . $ipadresi->deger ;
            //else
                return (!empty($_SERVER['HTTPS']) ? "https" : "https") . "://" . $ipadresi->deger ;
        }
    }

    public static function getStokKontrol($degisen) {//Stok Kontrolünü döndürür
        if($degisen!="") {
            $degisenler = explode(',', $degisen);
            $eksik = array();
            $stokdurum = StokDurum::whereIn('degisenler_id', $degisenler)->get();
            if($stokdurum->count()>0)
            {
                foreach ($stokdurum as $parca) {
                    if (($parca->kalan - $parca->kullanilan) < $parca->adet) { //eksik
                        $parca->degisen = Degisenler::find($parca->degisenler_id);
                        array_push($eksik, $parca->degisen);
                    }
                }

                $parcadurum = Degisenler::whereIn('id', $degisenler)->where('parcadurum', 1)->get();
                $parcalar = "";
                foreach ($parcadurum as $parca) {
                    $parcalar .= ($parcalar == "" ? "" : ",") . $parca->parcalar;
                }
                if ($parcalar != "") {
                    $parcalar = explode(',', $parcalar);
                    $parcastokdurum = StokDurum::whereIn('degisenler_id', $parcalar)->get();
                    foreach ($parcastokdurum as $parca) {
                        if (($parca->kalan - $parca->kullanilan) < $parca->adet) { //eksik
                            $parca->degisen = Degisenler::find($parca->degisenler_id);
                            array_push($eksik, $parca->degisen);
                        }
                    }

                }
                if (count($eksik) > 0)
                    return array('durum' => 0, 'eksik' => $eksik);
                else
                    return array('durum' => 1);
            }else{
                $parcadurum = Degisenler::whereIn('id', $degisenler)->where('parcadurum', 1)->get();
                $parcalar = "";
                foreach ($parcadurum as $parca) {
                    $parcalar .= ($parcalar == "" ? "" : ",") . $parca->parcalar;
                }
                if ($parcalar != "") {
                    $parcalar = explode(',', $parcalar);
                    $parcastokdurum = StokDurum::whereIn('degisenler_id', $parcalar)->get();
                    foreach ($parcastokdurum as $parca) {
                        if (($parca->kalan - $parca->kullanilan) < $parca->adet) { //eksik
                            $parca->degisen = Degisenler::find($parca->degisenler_id);
                            array_push($eksik, $parca->degisen);
                        }
                    }

                }
                if (count($eksik) > 0)
                    return array('durum' => 0, 'eksik' => $eksik);
                else
                    return array('durum' => 1);
            }
        }else{
            return array('durum' => 1);
        }
    }

    public static function getStokParcaKontrol($degisen) {
        $degisenler=explode(',', $degisen);
        $parcadurum = Degisenler::whereIn('id', $degisenler)->where('parcadurum', 1)->get();
        $parcalar = "";
        foreach ($parcadurum as $parca) {
            $parcalar .= ($parcalar == "" ? "" : ",") . $parca->parcalar;
        }
        return $parcalar;
    }

    public static function getStokKontrolUpdate($degisen,$eskidegisen) {
        $degisenler=explode(',', $degisen);
        $eskidegisenler=explode(',', $eskidegisen);
        $fark= BackendController::getStokFark($degisenler, $eskidegisenler);
        $stok_parca=BackendController::getStokParcaKontrol($fark);
        $fark.=($stok_parca==""?"":",").$stok_parca;
        $fark = explode(',',$fark);
        $eksik = array();
        $stokdurumlari = StokDurum::whereIn('degisenler_id',$fark)->get();
        foreach ($stokdurumlari as $parca) {
            if (($parca->kalan - $parca->kullanilan) < 1) { //eksik
                $parca->degisen = Degisenler::find($parca->degisenler_id);
                array_push($eksik, $parca->degisen);
            }
        }
        if (count($eksik) > 0)
            return array('durum' => 0, 'eksik' => $eksik);
        else
            return array('durum' => 1);
    }

    public static function getStokFark($degisenler,$eskidegisenler){
        $fark="";
        for($i=0;$i<count($degisenler);$i++)
        {
            $flag=0;
            for($j=0;$j<count($eskidegisenler);$j++)
            {
                if($degisenler[$i]==$eskidegisenler[$j])
                {
                    $flag=1;
                    break;
                }
            }
            if($flag==0)
            {
                $fark .= ($fark=="" ? "" : ",").$degisenler[$i];
            }
        }
        return $fark;
    }

    public static function getStokKullan($degisen,$servisid)
    {
        if($degisen!="") {
            $degisenler = explode(',', $degisen);
            $stokdurumlar = StokDurum::whereIn('degisenler_id', $degisenler)->where('servis_id', $servisid)->get();
            foreach ($stokdurumlar as $stokdurum) {
                $stokdurum->kullanilan += $stokdurum->adet;
                $stokdurum->save();
            }

            $parcadurum = Degisenler::whereIn('id', $degisenler)->where('parcadurum', 1)->get();
            $parcalar = "";
            foreach ($parcadurum as $parca) {
                $parcalar .= ($parcalar == "" ? "" : ",") . $parca->parcalar;
            }
            if ($parcalar != "") {
                $parcalar = explode(',', $parcalar);
                $parcastokdurum = StokDurum::whereIn('degisenler_id', $parcalar)->get();
                foreach ($parcastokdurum as $parca) {
                    $parca->kullanilan += $parca->adet;
                    $parca->save();
                }
            }
        }
    }

    public static function getStokGeriAl($degisen, $servisid) {
        try {
            if ($degisen != "") {
                $degisenler = explode(',', $degisen);
                $stokdurumlar = StokDurum::whereIn('degisenler_id', $degisenler)->where('servis_id', $servisid)->get();
                foreach ($stokdurumlar as $stokdurum) {
                    $stokdurum->kullanilan -= $stokdurum->adet;
                    $stokdurum->save();
                }

                $parcadurum = Degisenler::whereIn('id', $degisenler)->where('parcadurum', 1)->get();
                $parcalar = "";
                foreach ($parcadurum as $parca) {
                    $parcalar .= ($parcalar == "" ? "" : ",") . $parca->parcalar;
                }
                if ($parcalar != "") {
                    $parcalar = explode(',', $parcalar);
                    $parcastokdurum = StokDurum::whereIn('degisenler_id', $parcalar)->get();
                    foreach ($parcastokdurum as $parca) {
                        $parca->kullanilan -= $parca->adet;
                        $parca->save();
                    }
                }
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getKullaniciServis($status=0){
        $user=Auth::user();
        if($user->servis_id==0) //yetkili hepsini görür
        {
            $servisid=array("1","2","3","4","5","6");
        }else if($user->servis_id==1 || $user->servis_id==4 ) //su isi gaz
        {
            $servisid=array("1","3","4");
        }else if($user->servis_id==2) //elektrik
        {
            $servisid=array("2");
        }else if($user->servis_id==3) //gaz
        {
            $servisid=array("3");
        }else if($user->servis_id==5) // gaz mekanik
        {
            $servisid=array("5");
        }else if($user->servis_id==6) // şube
        {   if($status==1)
                $servisid=array("6");
            else
                $servisid=array("1","2","3","4","5","6");
        }else{ //kullanıcı
            $servisid=array("1","2","3","4","5","6");
        }
        return $servisid;
    }

    public static function getServisSayacTur($servisid){
        if($servisid==6) //sube
        {
            if(Auth::user()->grup_id<5)
            {
                $sayactur=array(1,2,3,4,5);
            }else{
                $sube=Sube::whereIn('netsiscari_id',Auth::user()->netsiscari_id)->first();
                if($sube){
                    $sayactur=explode(',',$sube->sayactur);
                }else{
                    $sayactur=array();
                }
            }
        }else{
            $sayactur=array($servisid);
        }
        return $sayactur;
    }

    public static function getSayacTurId($sayacadi_id){
        $sayacadi = SayacAdi::find($sayacadi_id);
        return $sayacadi->sayactur_id;
    }

    public static function getKullaniciServisKodu(){
        $user=Auth::user();
        if($user->servis_id==0) //yetkili hepsini görür
        {
            $servisid=array("SU","ELEKTRNK","DOGALGAZ","ISI");
        }else if($user->servis_id==1 || $user->servis_id==4 ) //su isi
        {
            $servisid=array("SU","ISI","DOGALGAZ");
        }else if($user->servis_id==2) //elektrik
        {
            $servisid=array("ELEKTRNK");
        }else if($user->servis_id==3 || $user->servis_id==5) //gaz mekanik
        {
            $servisid=array("DOGALGAZ");
        }else if($user->servis_id==6) //sube
        {
            $servisid=array("SU","ELEKTRNK","DOGALGAZ","ISI");
        }else{
            $servisid=array("HEPSI");
        }
        return $servisid;
    }

    public static function getServisKodu($id){
        switch($id){
            case 1: return "SU";
            case 2: return "ELEKTRNK";
            case 3: return "DOGALGAZ";
            case 4: return "ISI";
            case 5: return "DOGALGAZ";
            case 6: return "SUBE";
        }
        return "";
    }

    public static function getServisWithServisStokKodu($serviskodu){
        $servisstokkod=ServisStokKod::where('stokkodu',$serviskodu)->where('koddurum',true)->first();
        if($servisstokkod){
            return $servisstokkod->servisid;
        }else{
            return 0;
        }
    }

    public static function getServisAdi($id){
        switch ($id){
            case 1 : return 'Su Servis';
            case 2 : return 'Elektrik Servis';
            case 3 : return 'Gaz Servis';
            case 4 : return 'Isı Servis';
            case 5 : return 'Gaz Servis Mekanik';
            case 6 : return 'Şube';
            default : return '';
        }
    }

    public static function getProblemler($sayacarizaid){
        $sayacariza = SayacAriza::find($sayacarizaid);
        $problemler = explode(',',$sayacariza->problemler);
        $arizakodlar = ArizaKod::whereIn('id',$problemler)->get();
        $tanim="";
        foreach($arizakodlar as $arizakod){
            $tanim.=($tanim=="" ? "" : ",").$arizakod->tanim;
        }
        return $tanim;
    }

    public static function getYapilanIslemler($sayacyapilanid){
        $sayacyapilan = SayacYapilan::find($sayacyapilanid);
        $yapilanisler = explode(',',$sayacyapilan->yapilanlar);
        $yapilanlar = Yapilanlar::whereIn('id',$yapilanisler)->get();
        $tanim="";
        foreach($yapilanlar as $yapilan){
            $tanim.=($tanim=="" ? "" : ",").$yapilan->tanim;
        }
        return $tanim;
    }

    public static function getDegisenlerParca($sayacdegisenid){
        $sayacdegisen = SayacDegisen::find($sayacdegisenid);
        $parcalar = explode(',',$sayacdegisen->degisenler);
        $degisenler = Degisenler::whereIn('id',$parcalar)->get();
        $tanim="";
        foreach($degisenler as $degisen){
            $tanim.=($tanim=="" ? "" : ",").$degisen->tanim;
        }
        return $tanim;
    }

    public static function getSonucUyarilar($sayacuyariid){
        $sayacuyari = SayacUyari::find($sayacuyariid);
        $uyarilist = explode(',',$sayacuyari->uyarilar);
        $uyarilar = Uyarilar::whereIn('id',$uyarilist)->get();
        $tanim="";
        foreach($uyarilar as $uyari){
            $tanim.=($tanim=="" ? "" : ",").$uyari->tanim;
        }
        return $tanim;
    }

    public static function getAksesuar($aksesuarlar){
        $aksesuarlist=explode(',',$aksesuarlar);
        $tanim="";
        if($aksesuarlar!="" || $aksesuarlar!=null){
            $ekliaksesuarlar = Aksesuar::whereIn('id',$aksesuarlist)->get();
            foreach($ekliaksesuarlar as $aksesuar){
                $tanim.=($tanim=="" ? "" : ",").$aksesuar->adi;
            }
        }else{
            $tanim="YOK";
        }
        return $tanim;
    }

    public static function getDegisenParcalar($sayacdegisenid){
        $sayacdegisen = SayacDegisen::find($sayacdegisenid);
        $parcalar = explode(',',$sayacdegisen->degisenler);
        $degisenler = Degisenler::whereIn('id',$parcalar)->get();
        $tanim=array();
        foreach($degisenler as $degisen){
            array_push($tanim,$degisen->tanim);
        }
        return $tanim;
    }

    public static function getListeFark($tumu,$secilenler){
        $fark="";
        foreach($tumu as $secilen)
        {
            if(!in_array($secilen,$secilenler)){
                $fark .= ($fark=="" ? "" : ",").$secilen;
            }
        }
        /*for($i=0;$i<count($tumu);$i++)
        {
            $flag=0;
            for($j=0;$j<count($secilenler);$j++)
            {

                if($tumu[$i]==$secilenler[$j])
                {
                    $flag=1;
                    break;
                }
            }
            if($flag==0)
            {
                $fark .= ($fark=="" ? "" : ",").$tumu[$i];
            }
        }*/
        return $fark;
    }

    public static function getListeFarkArray($tumu,$secilenler){
        $fark=array();
        foreach($tumu as $secilen)
        {
            if(!in_array($secilen,$secilenler)){
                array_push($fark,$secilen);
            }
        }
        /*for($i=0;$i<count($tumu);$i++)
        {
            $flag=0;
            for($j=0;$j<count($secilenler);$j++)
            {
                if($tumu[$i]==$secilenler[$j])
                {
                    $flag=1;
                    break;
                }
            }
            if($flag==0)
            {
                array_push($fark,$tumu[$i]);
            }
        }*/
        return $fark;
    }

    public static function getListeEleman($tumu,$secilenler){
        $liste=array();
        for($i=0;$i<count($tumu);$i++)
        {
            for($j=0;$j<count($secilenler);$j++)
            {
                if($tumu[$i]==$secilenler[$j])
                {
                    array_push($liste,$tumu[$i]);
                }
            }
        }
        return $liste;
    }

    public static function FaturaGrupla($arizafiyatlist,$depolararasi=false){
        $list=$toplamtutar=$arizagaranti=$servisstokkod=array();
        foreach ($arizafiyatlist as $key => $row) {
            $arizagaranti[$key]  = $row['ariza_garanti'];
            $toplamtutar[$key] = $row['toplamtutar'];
            $servisstokkod[$key] = $row['servisstokkodu'];
        }
        if($depolararasi){
            array_multisort($servisstokkod, SORT_ASC, $arizafiyatlist);
            $i=0;
            foreach($arizafiyatlist as $arizafiyat)
            {
                if($i==0)
                {
                    array_push($list,$arizafiyat);
                    $i++;
                }else{
                    if($list[$i-1]['servisstokkodu']==$arizafiyat['servisstokkodu']) {
                        $list[$i - 1]['adet'] += 1;
                    } else {
                        array_push($list,$arizafiyat);
                        $i++;
                    }
                }
            }
        }else{
            array_multisort($arizagaranti, SORT_DESC, $servisstokkod, SORT_ASC, $toplamtutar, SORT_ASC, $arizafiyatlist);
            $i=0;
            foreach($arizafiyatlist as $arizafiyat)
            {
                if($i==0)
                {
                    array_push($list,$arizafiyat);
                    $i++;
                }else{
                    if($list[$i-1]['ariza_garanti']==$arizafiyat['ariza_garanti']){
                        if($list[$i-1]['servisstokkodu']==$arizafiyat['servisstokkodu']) {
                            if ($list[$i - 1]['toplamtutar'] == $arizafiyat['toplamtutar']) {
                                $list[$i - 1]['adet'] += 1;
                            } else {
                                array_push($list, $arizafiyat);
                                $i++;
                            }
                        }else{
                            array_push($list,$arizafiyat);
                            $i++;
                        }
                    }else{
                        array_push($list,$arizafiyat);
                        $i++;
                    }
                }
            }
        }
        return $list;
    }

    public static function NetsisFatura($depoteslimid,$durum){ //durum 1 fatura edilmeyecekse
        $sabit = SistemSabitleri::where('adi','NetsisFatura')->first();
        if($sabit->deger==0) //fatura kesilmeyecek
        {
            return 0;
        }else{
            try{
                $depoteslim = DepoTeslim::find($depoteslimid);
                $netsiscari = NetsisCari::find($depoteslim->netsiscari_id);
                $vadegunu = is_null($netsiscari->vadegunu) ? 0 : $netsiscari->vadegunu;
                $odemetarih = date('Y-m-d', strtotime(date('Y-m-d') . ' + ' . $vadegunu . ' days'));
                $secilenler = explode(',', $depoteslim->secilenler);
                $arizafiyatlar = ArizaFiyat::whereIn('sayacgelen_id', $secilenler)->get();
                $birimdurum = ArizaFiyat::whereIn('sayacgelen_id', $secilenler)->whereNotNull('parabirimi2_id')->count();
                if($birimdurum>0){ // 2 parabirimi mevcutsa herşeyi tl ye çevir
                    foreach ($arizafiyatlar as $arizafiyat) {
                        $depogelen=DepoGelen::find($arizafiyat->depogelen_id);
                        $arizafiyat->servisstokkodu = $depogelen->servisstokkodu;
                        $arizafiyat->adet=1;
                        if($arizafiyat->parabirimi_id!=1){
                            $dovizkuru = DovizKuru::where('parabirimi_id', $arizafiyat->parabirimi_id)->where('tarih', date('Y-m-d'))->first();
                            if (!$dovizkuru)
                                return 5;
                            $kur = $dovizkuru->kurfiyati;
                            $arizafiyat->toplamtutar *= $kur;
                            $arizafiyat->parabirimi_id = 1;
                        }
                        if($arizafiyat->parabirimi2_id!=null){
                            if($arizafiyat->parabirimi2_id!=1){
                                $dovizkuru = DovizKuru::where('parabirimi_id', $arizafiyat->parabirimi2_id)->where('tarih', date('Y-m-d'))->first();
                                if (!$dovizkuru)
                                    return 5;
                                $kur = $dovizkuru->kurfiyati;
                                $arizafiyat->toplamtutar += ($arizafiyat->toplamtutar2*$kur);
                            }else{
                                $arizafiyat->toplamtutar += ($arizafiyat->toplamtutar2);
                            }
                            $arizafiyat->toplamtutar2=0;
                            $arizafiyat->parabirimi2_id=null;
                        }
                    }
                }else{
                    foreach ($arizafiyatlar as $arizafiyat) {
                        $depogelen=DepoGelen::find($arizafiyat->depogelen_id);
                        $arizafiyat->servisstokkodu = $depogelen->servisstokkodu;
                        $arizafiyat->adet=1;
                    }
                }
                $arizafiyatlist=$arizafiyatlar->toArray();
                $faturano=Fatuno::where('SUBE_KODU',8)->where('SERI','S')->where('TIP','6')->first();
                if(!$faturano){
                    $faturano=new Fatuno;
                    $faturano->SUBE_KODU=8;
                    $faturano->SERI='S';
                    $faturano->TIP=6;
                    $faturano->NUMARA='SR'.'0';
                    $faturano->save();
                }
                $fatirsno=BackendController::FaturaNo($faturano->NUMARA,1);
                Fatuno::where(['SUBE_KODU'=>8,'SERI' => 'S','TIP' => '6'])->update(['NUMARA' => $fatirsno]);
                $fatura=Fatuirs::where('SUBE_KODU',8)->where('FTIRSIP',6)->where('FATIRS_NO',$fatirsno)->first();
                if($fatura){
                    $sonfatura=Sipamas::where('SUBE_KODU',8)->where('FTIRSIP',6)->where('FATIRS_NO','LIKE','SR%')->orderBy('FATIRS_NO','desc')->first();
                    if($sonfatura){
                        $fatirsno =  BackendController::FaturaNo($sonfatura->FATIRS_NO,1);
                        Fatuno::where(['SUBE_KODU'=>8,'SERI' => 'S','TIP' => '6'])->update(['NUMARA' => $fatirsno]);
                    }else{
                        return 6;
                    }
                }
                try {
                    $fatura = new Sipamas;
                    $faturaek = new Fatuek;
                    $fatura->SUBE_KODU = 8;
                    $fatura->FATIRS_NO = $fatirsno;  //ornek A00000000633911
                    $fatura->FTIRSIP = '6';
                    $fatura->CARI_KODU = BackendController::ReverseTrk($depoteslim->carikod);
                    $fatura->TARIH = date('Y-m-d');
                    $fatura->TIPI = 2;
                    $fatura->ACIKLAMA = BackendController::ReverseTrk($depoteslim->aciklama);
                    $fatura->KOD1 = $depoteslim->ozelkod;
                    $fatura->ODEMEGUNU =$vadegunu;
                    $fatura->ODEMETARIHI = $odemetarih;
                    $fatura->KDV_DAHILMI = 'E';
                    $fatura->SIPARIS_TEST = date('Y-m-d');
                    $fatura->PLA_KODU = $depoteslim->plasiyerkod;
                    $fatura->D_YEDEK10 = date('Y-m-d');
                    $fatura->PROJE_KODU = '1';
                    $fatura->KAYITYAPANKUL = $depoteslim->netsiskullanici;
                    $fatura->KAYITTARIHI = date('Y-m-d H:i:s');
                    $fatura->ISLETME_KODU = 1;

                    $faturaek->SUBE_KODU = 8;
                    $faturaek->FKOD = '6';
                    $faturaek->FATIRSNO = $fatirsno;
                    $faturaek->CKOD = BackendController::ReverseTrk($depoteslim->carikod);
                    $faturaek->ACIK3 = BackendController::ReverseTrk($depoteslim->teslimadres);
                    $faturaek->ACIK7 = BackendController::ReverseTrk($depoteslim->belge1);
                    $faturaek->ACIK8 = BackendController::ReverseTrk($depoteslim->belge2);
                    $faturaek->ACIK1 = BackendController::ReverseTrk($depoteslim->belge3);
                    $faturaek->save();
                    $tutar = 0;
                    $kalemler = BackendController::FaturaGrupla($arizafiyatlist);
                    $i = 1;
                    $inckey = array();
                    try {
                        foreach ($kalemler as $kalem) {
                            $faturakalem = new Sipatra;
                            $faturakalem->STOK_KODU = $kalem['servisstokkodu'];
                            $faturakalem->FISNO = $fatirsno;
                            $faturakalem->STHAR_GCMIK = $kalem['adet'];
                            $faturakalem->STHAR_GCKOD = 'C';
                            $faturakalem->STHAR_TARIH = date('Y-m-d');
                            if ($durum == 1) {
                                $faturakalem->STHAR_NF = 0;
                                $faturakalem->STHAR_BF = 0;
                                $faturakalem->STHAR_DOVTIP = 0;
                                $faturakalem->STHAR_DOVFIAT = 0;
                            } else {
                                $parabirimi = $kalem['parabirimi_id'];
                                if ($parabirimi != 1) {
                                    $dovizkuru = DovizKuru::where('parabirimi_id', $parabirimi)->where('tarih', date('Y-m-d'))->first();
                                    if (!$dovizkuru)
                                        return 5;
                                    $kur = $dovizkuru->kurfiyati;
                                    $doviztutar = $kalem['toplamtutar'];
                                    $kalemtutar = $doviztutar * $kur;
                                    $kalemkdv = round(($kalemtutar * 18) / 118, 4);
                                    $kalembrut = $kalemtutar - $kalemkdv;
                                    $faturakalem->STHAR_NF = $kalembrut;
                                    $faturakalem->STHAR_BF = $kalemtutar;
                                    $faturakalem->STHAR_DOVTIP = $parabirimi;
                                    $faturakalem->STHAR_DOVFIAT = $doviztutar;
                                } else {
                                    $kalemtutar = $kalem['toplamtutar'];
                                    $kalemkdv = round(($kalemtutar * 18) / 118, 4);
                                    $kalembrut = $kalemtutar - $kalemkdv;
                                    $faturakalem->STHAR_NF = $kalembrut;
                                    $faturakalem->STHAR_BF = $kalemtutar;
                                    $faturakalem->STHAR_DOVTIP = 0;
                                    $faturakalem->STHAR_DOVFIAT = 0;
                                }
                            }
                            $faturakalem->STHAR_KDV = 18;
                            $faturakalem->DEPO_KODU = $depoteslim->depokodu;
                            $faturakalem->STHAR_ACIKLAMA = BackendController::ReverseTrk($depoteslim->carikod);
                            $faturakalem->STHAR_FTIRSIP = '6';
                            $faturakalem->LISTE_FIAT = 1;
                            $faturakalem->STHAR_HTUR = 'H';
                            $faturakalem->STHAR_ODEGUN = $vadegunu;
                            $faturakalem->STHAR_BGTIP = 'I';
                            $faturakalem->STHAR_KOD1 = $depoteslim->ozelkod;
                            $faturakalem->STHAR_KOD2 = 'B';
                            $faturakalem->STHAR_CARIKOD = BackendController::ReverseTrk($depoteslim->carikod);
                            $faturakalem->STHAR_SIP_TURU = 'B';
                            $faturakalem->PLASIYER_KODU = $depoteslim->plasiyerkod;
                            $faturakalem->SIRA = $i;
                            $faturakalem->STRA_SIPKONT = $i;
                            $faturakalem->IRSALIYE_TARIH = date('Y-m-d');
                            $faturakalem->STHAR_TESTAR = date('Y-m-d');
                            $faturakalem->OLCUBR = 1;
                            $faturakalem->VADE_TARIHI = $odemetarih;
                            $faturakalem->SUBE_KODU = 8;
                            $faturakalem->C_YEDEK6 = 'K';
                            $faturakalem->D_YEDEK10 = date('Y-m-d');
                            $faturakalem->PROJE_KODU = '1';
                            $tutar += ($faturakalem->STHAR_GCMIK * $faturakalem->STHAR_BF);
                            $i++;
                            $faturakalem->save();
                            $id = DB::connection('sqlsrv2')->select("select top(1)INCKEYNO from tblsipatra where SUBE_KODU=8 and FISNO='" . $fatirsno . "' order by INCKEYNO desc");
                            $inckeyno = ($id[0]->INCKEYNO);
                            if (in_array($inckeyno, $inckey)) { //girilen inckey zaten ekliyse kaydedememiş demektir
                                $fatura->delete();
                                $faturaek->delete();
                                Sipatra::where('STHAR_FTIRSIP','6')->where('SUBE_KODU',8)
                                    ->where('STHAR_CARIKOD',BackendController::ReverseTrk($depoteslim->carikod))
                                    ->where('FISNO',BackendController::ReverseTrk($fatirsno))->delete();
                                return 7;
                            } else { //kaydetme başarılı
                                array_push($inckey, $inckeyno);
                            }
                        }
                    } catch (Exception $e) {
                        Log::error($e);
                        $fatura->delete();
                        $faturaek->delete();
                        Sipatra::where('STHAR_FTIRSIP','6')->where('SUBE_KODU',8)
                            ->where('STHAR_CARIKOD',BackendController::ReverseTrk($depoteslim->carikod))
                            ->where('FISNO',BackendController::ReverseTrk($fatirsno))->delete();
                        return 4;
                    }
                    $kdv = round(($tutar * 18) / 118, 2);
                    $brut = $tutar - $kdv;
                    $fatura->BRUTTUTAR = $brut; //toplam
                    $fatura->KDV = $kdv; //kdv si
                    $fatura->FATKALEM_ADEDI = ($i - 1); //kalem adedi
                    $fatura->GENELTOPLAM = $tutar; //genel toplam
                    $fatura->save();
                    $depoteslim->db_name = Config::get('database.connections.sqlsrv2.database');
                    $depoteslim->faturano = $fatirsno;
                    $depoteslim->save();
                    return 1;
                } catch (Exception $e) {
                    Log::error($e);
                    $fatura->delete();
                    return 2;
                }
            }catch (Exception $e){
                Log::error($e);
                return 3;
            }
        }
    }

    public static function NetsisDepolararasi($depoteslimid){
        try{
            $depoteslim = DepoTeslim::find($depoteslimid);
            $secilenler = explode(',', $depoteslim->secilenler);
            $arizafiyatlar = ArizaFiyat::whereIn('sayacgelen_id', $secilenler)->get();
            $netsiscari=NetsisCari::find($depoteslim->netsiscari_id);
            $vadegunu = is_null($netsiscari->vadegunu) ? 0 : $netsiscari->vadegunu;
            $odemetarih = date('Y-m-d', strtotime(date('Y-m-d') . ' + ' . $vadegunu . ' days'));
            $kasa=KasaKod::where('subekodu',8)->first();
            foreach ($arizafiyatlar as $arizafiyat) {
                $depogelen=DepoGelen::find($arizafiyat->depogelen_id);
                $arizafiyat->servisstokkodu = $depogelen->servisstokkodu;
                $arizafiyat->adet=1;
            }
            $arizafiyatlist=$arizafiyatlar->toArray();
            $kalemler=BackendController::FaturaGrupla($arizafiyatlist,1);
            $faturano=Fatuno::where('SUBE_KODU',8)->where('SERI','A')->where('TIP','6')->first();
            if(!$faturano){
                $faturano=new Fatuno;
                $faturano->SUBE_KODU=8;
                $faturano->SERI='A';
                $faturano->TIP=6;
                $faturano->NUMARA='A'.'0';
                $faturano->save();
            }
            $fatirsno =  BackendController::FaturaNo($faturano->NUMARA,1);
            Fatuno::where(['SUBE_KODU'=>8,'SERI' => 'A','TIP' => '6'])->update(['NUMARA' => $fatirsno]);
            $fatura=Sipamas::where('SUBE_KODU',8)->where('FTIRSIP',6)->where('FATIRS_NO',$fatirsno)->first();
            if($fatura){
                $sonfatura=Sipamas::where('SUBE_KODU',8)->where('FTIRSIP',6)->where('FATIRS_NO','LIKE','A%')->orderBy('FATIRS_NO','desc')->first();
                if($sonfatura){
                    $fatirsno =  BackendController::FaturaNo($sonfatura->FATIRS_NO,1);
                    Fatuno::where(['SUBE_KODU'=>8,'SERI' => 'A','TIP' => '6'])->update(['NUMARA' => $fatirsno]);
                }else{
                    return 6;
                }
            }
            $fatura = null;
            $faturaek = null;
            try {
                $fatura = new Sipamas;
                $faturaek = new Fatuek;
                $fatura->SUBE_KODU = 8;
                $fatura->FATIRS_NO = $fatirsno;  //ornek A00000000633911
                $fatura->FTIRSIP = '6';
                $fatura->CARI_KODU = BackendController::ReverseTrk($depoteslim->carikod);
                $fatura->TARIH = date('Y-m-d');
                $fatura->TIPI = 2;
                $fatura->BRUTTUTAR = 0;
                $fatura->KDV = 0;
                $fatura->GENELTOPLAM = 0;
                $fatura->KOD1 = $depoteslim->ozelkod;
                $fatura->ODEMEGUNU =$vadegunu;
                $fatura->ODEMETARIHI = $odemetarih;
                $fatura->KDV_DAHILMI = 'H';
                $fatura->SIPARIS_TEST = date('Y-m-d');
                $fatura->PLA_KODU = $depoteslim->plasiyerkod;
                $fatura->KS_KODU = $kasa->kasakod;
                $fatura->C_YEDEK6 = 'X';
                $fatura->D_YEDEK10 = date('Y-m-d');
                $fatura->PROJE_KODU = '1';
                $fatura->KAYITYAPANKUL = $depoteslim->netsiskullanici;
                $fatura->KAYITTARIHI = date('Y-m-d H:i:s');
                $fatura->DUZELTMEYAPANKUL = $depoteslim->netsiskullanici;
                $fatura->DUZELTMETARIHI = date('Y-m-d H:i:s');
                $fatura->ISLETME_KODU = 1;

                $faturaek->SUBE_KODU = 8;
                $faturaek->FKOD = '6';
                $faturaek->FATIRSNO = $fatirsno;
                $faturaek->CKOD = BackendController::ReverseTrk($depoteslim->carikod);
                $faturaek->ACIK4 = $depoteslim->teslimadres;
                $faturaek->ACIK7 = $depoteslim->belge1;
                $faturaek->ACIK8 = $netsiscari->cariadi;
                $faturaek->save();
                $i = 1;
                $inckey = array();
                try {
                    foreach ($kalemler as $kalem) {
                        $faturakalem = new Sipatra;
                        $faturakalem->STOK_KODU = $kalem['servisstokkodu'];
                        $faturakalem->FISNO = $fatirsno;
                        $faturakalem->STHAR_GCMIK = $kalem['adet'];
                        $faturakalem->STHAR_GCKOD = 'C';
                        $faturakalem->STHAR_TARIH = date('Y-m-d');
                        $faturakalem->STHAR_NF = 0;
                        $faturakalem->STHAR_BF = 0;
                        $faturakalem->STHAR_DOVTIP = 0;
                        $faturakalem->STHAR_DOVFIAT = 0;
                        $faturakalem->STHAR_KDV = 18;
                        $faturakalem->DEPO_KODU = $depoteslim->depokodu;
                        $faturakalem->STHAR_ACIKLAMA = BackendController::ReverseTrk($depoteslim->carikod);
                        $faturakalem->STHAR_FTIRSIP = '6';
                        $faturakalem->LISTE_FIAT = 1;
                        $faturakalem->STHAR_HTUR = 'H';
                        $faturakalem->STHAR_ODEGUN = $vadegunu;
                        $faturakalem->STHAR_BGTIP = 'I';
                        $faturakalem->STHAR_KOD1 = $depoteslim->ozelkod;
                        $faturakalem->STHAR_KOD2 = 'B';
                        $faturakalem->STHAR_CARIKOD = BackendController::ReverseTrk($depoteslim->carikod);
                        $faturakalem->STHAR_SIP_TURU = 'B';
                        $faturakalem->PLASIYER_KODU = $depoteslim->plasiyerkod;
                        $faturakalem->SIRA = $i;
                        $faturakalem->STRA_SIPKONT = $i;
                        $faturakalem->IRSALIYE_TARIH = date('Y-m-d');
                        $faturakalem->STHAR_TESTAR = date('Y-m-d');
                        $faturakalem->OLCUBR = 1;
                        $faturakalem->VADE_TARIHI = $odemetarih;
                        $faturakalem->SUBE_KODU = 8;
                        $faturakalem->C_YEDEK6 = 'K';
                        $faturakalem->D_YEDEK10 = date('Y-m-d');
                        $faturakalem->PROJE_KODU = '1';
                        $faturakalem->save();
                        $i++;
                        $id = DB::connection('sqlsrv2')->select("select top(1)INCKEYNO from tblsipatra where SUBE_KODU=8 and FISNO='" . $fatirsno . "' order by INCKEYNO desc");
                        $inckeyno = ($id[0]->INCKEYNO);
                        if (in_array($inckeyno, $inckey)) { //girilen inckey zaten ekliyse kaydedememiş demektir
                            $fatura->delete();
                            $faturaek->delete();
                            Sipatra::where('STHAR_FTIRSIP','6')->where('SUBE_KODU',8)
                                ->where('STHAR_CARIKOD',BackendController::ReverseTrk($depoteslim->carikod))
                                ->where('FISNO',BackendController::ReverseTrk($fatirsno))->delete();
                            return 7;
                        } else { //kaydetme başarılı
                            array_push($inckey, $inckeyno);
                        }
                    }
                } catch (Exception $e) {
                    Log::error($e);
                    $fatura->delete();
                    $faturaek->delete();
                    return 4;
                }
                $fatura->FATKALEM_ADEDI = ($i - 1); //kalem adedi
                $fatura->save();
                $depoteslim->db_name = Config::get('database.connections.sqlsrv2.database');
                $depoteslim->faturano = $fatirsno;
                $depoteslim->save();
                return 1;
            } catch (Exception $e) {
                Log::error($e);
                $fatura->delete();
                $faturaek->delete();
                return 2;
            }
        }catch (Exception $e){
            Log::error($e);
            return 3;
        }
    }

    public static function getBakimDurum(){
        $durum = SistemSabitleri::where('adi','BakimDurum')->first();
        return $durum->deger;
    }

    public static function getFiyatlandirma($ucretlendirilenid)
    {
        try {
            $ucretlendirilen = Ucretlendirilen::find($ucretlendirilenid);
            if ($ucretlendirilen) {
                $ucretlendirilen->uretimyer = UretimYer::find($ucretlendirilen->uretimyer_id);
                $ucretlendirilen->yetkili = Yetkili::where('netsiscari_id', $ucretlendirilen->netsiscari_id)->where('aktif', 1)->first();
                if (!$ucretlendirilen->yetkili) {
                    return Redirect::back()->with(array('mesaj' => 'true', 'text' => 'Seçilen yerin yetkili kişisi ekli değil', 'type' => 'warning', 'title' => 'Rapor Hatası'));
                }
                $raporadi = "Fiyatlandirma_" . Str::slug($ucretlendirilen->uretimyer->yeradi);
                $export = "pdf";
                $kriterler = array();
                $kriterler['id'] = $ucretlendirilenid;
                if(file_exists(public_path('reports/outputs/fiyatlandirma/' . $raporadi.'.'.$export)))
                    File::delete(public_path( 'reports/outputs/fiyatlandirma/' . $raporadi.'.'.$export));
                JasperPHP::process(public_path('reports/fiyatlandirma/fiyatlandirma.jasper'), public_path('reports/outputs/fiyatlandirma/' . $raporadi),
                    array($export), $kriterler,
                    Config::get('database.connections.report'))->execute();
                return true;
            } else {
                return null;
            }
        } catch (Exception $e) {
            Log::error($e);
            return null;
        }
    }

    public static function getDetaylifiyatlandirma($ucretlendirilenid)
    {
        try {
            $ucretlendirilen = Ucretlendirilen::find($ucretlendirilenid);
            if ($ucretlendirilen) {
                $ucretlendirilen->uretimyer = UretimYer::find($ucretlendirilen->uretimyer_id);
                $ucretlendirilen->yetkili = Yetkili::where('netsiscari_id', $ucretlendirilen->netsiscari_id)->where('aktif', 1)->first();
                if (!$ucretlendirilen->yetkili) {
                    return Redirect::back()->with(array('mesaj' => 'true', 'text' => 'Seçilen yerin yetkili kişisi ekli değil', 'type' => 'warning', 'title' => 'Rapor Hatası'));
                }
                $raporadi = "Fiyat_Raporu_" . Str::slug($ucretlendirilen->uretimyer->yeradi);
                $export = "pdf";
                $kriterler = array();
                $kriterler['id'] = $ucretlendirilenid;
                if($ucretlendirilen->servis_id==5){
                    if(file_exists(public_path('reports/outputs/fiyatraporu/' . $raporadi.'.'.$export)))
                        File::delete(public_path( 'reports/outputs/fiyatraporu/' . $raporadi.'.'.$export));
                    JasperPHP::process(public_path('reports/fiyatraporu/fiyatraporugaz.jasper'), public_path('reports/outputs/fiyatraporu/' . $raporadi),
                        array($export), $kriterler,
                        Config::get('database.connections.report'))->execute();
                }else{
                    if(file_exists(public_path('reports/outputs/fiyatraporu/' . $raporadi.'.'.$export)))
                        File::delete(public_path( 'reports/outputs/fiyatraporu/' . $raporadi.'.'.$export));
                    JasperPHP::process(public_path('reports/fiyatraporu/fiyatraporu.jasper'), public_path('reports/outputs/fiyatraporu/' . $raporadi),
                        array($export), $kriterler,
                        Config::get('database.connections.report'))->execute();
                }
                return true;
            } else {
                return null;
            }
        } catch (Exception $e) {
            Log::error($e);
            return null;
        }
    }

    public static function getOnayFormu($ucretlendirilenid)
    {
        try {
            $ucretlendirilen = Ucretlendirilen::find($ucretlendirilenid);
            if ($ucretlendirilen) {
                $ucretlendirilen->uretimyer = UretimYer::find($ucretlendirilen->uretimyer_id);
                $ucretlendirilen->yetkili = Yetkili::where('netsiscari_id', $ucretlendirilen->netsiscari_id)->where('aktif', 1)->first();
                if (!$ucretlendirilen->yetkili) {
                    return Redirect::back()->with(array('mesaj' => 'true', 'text' => 'Seçilen yerin yetkili kişisi ekli değil', 'type' => 'warning', 'title' => 'Rapor Hatası'));
                }
                $raporadi = "OnayFormu_" . Str::slug($ucretlendirilen->uretimyer->yeradi);
                $export = "pdf";
                $kriterler = array();
                $kriterler['id'] = $ucretlendirilenid;
                if(file_exists(public_path('reports/outputs/onayformu/' . $raporadi.'.'.$export)))
                    File::delete(public_path('reports/outputs/onayformu/' . $raporadi.'.'.$export));
                JasperPHP::process(public_path('reports/onayformu/onayformu.jasper') ,public_path( 'reports/outputs/onayformu/' . $raporadi),
                    array($export), $kriterler,
                    Config::get('database.connections.report'))->execute();
                return true;
            } else {
                return null;
            }
        } catch (Exception $e) {
            Log::error($e);
            null;
        }
    }

    public static function getTeslimListesi($depoteslimid)
    {
        try {
            $depoteslim = DepoTeslim::find($depoteslimid);
            if ($depoteslim) {
                $depoteslim->netsiscari = NetsisCari::find($depoteslim->netsiscari_id);
                $raporadi = mb_substr("SayacListesi_" . Str::slug($depoteslim->netsiscari->cariadi),0,20);
                $export = "pdf";
                $kriterler = array();
                $kriterler['id'] = $depoteslimid;
                if(file_exists(public_path('reports/outputs/sayaclistesi/' . $raporadi.'.'.$export)))
                    File::delete(public_path('reports/outputs/sayaclistesi/' . $raporadi.'.'.$export));
                JasperPHP::process(public_path('reports/sayaclistesi/sayaclistesi.jasper') , public_path('reports/outputs/sayaclistesi/' . $raporadi) ,
                    array($export), $kriterler,
                    Config::get('database.connections.report'))->execute();
                return true;
            } else {
                return null;
            }
        } catch (Exception $e) {
            Log::error($e);
            null;
        }
    }

    public static function getRealIP(){
        if( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] )) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            if( isset( $_SERVER['REMOTE_ADDR'] ) )
                return $_SERVER['REMOTE_ADDR'];
            else
                return '';
        }
    }

    public static function DepoGirisGrupla($serinolar,$uretimyerleri,$serviskodlari,$sayacadlari,$sayaccaplari,$uretimyillari=false,$endeks=false){

        $list=$sayaclar=$serinolist=$uretimyerilist=$servisstokkod=$sayacadilist=$sayaccaplist=array();
        for($i=0;$i<count($serinolar);$i++)
        {
            $uretimyeri=$uretimyerleri[$i];
            $serino=$serinolar[$i];
            $sayacadi=$sayacadlari[$i];
            $sayaccap=$sayaccaplari[$i];
            $serviskod=$serviskodlari[$i];
            if(isset($uretimyillari[$i]))
                $uretimyili=$uretimyillari[$i];
            else
                $uretimyili='';
            if(isset($endeks[$i]))
                $endeksi = $endeks[$i];
            else
                $endeksi = '';
            if($serino!="")
            {
                array_push($sayaclar,array('serino'=>$serino,'sayacadi'=>$sayacadi,'sayaccap'=>$sayaccap,'serviskod'=>$serviskod
                ,'uretimyeri'=>$uretimyeri,'uretimyili'=> $uretimyili, 'endeks' => $endeksi,'adet'=>1));
            }
        }

        if(count($sayaclar)>0) {
            foreach ($sayaclar as $key => $row) {
                $serinolist[$key] = $row['serino'];
                $uretimyerilist[$key] = $row['uretimyeri'];
                $sayacadilist[$key] = $row['sayacadi'];
                $sayaccaplist[$key] = $row['sayaccap'];
                $servisstokkod[$key] = $row['serviskod'];
            }

            array_multisort($servisstokkod, SORT_ASC, $sayaclar);
            $i = 0;
            foreach ($sayaclar as $sayac) {
                if ($i == 0) {
                    array_push($list, array('kod' => $sayac['serviskod'], 'sayac' => array($sayac), 'adet' => $sayac['adet']));
                    $i++;
                } else {
                    if ($list[$i - 1]['kod'] == $sayac['serviskod']) {
                        $list[$i - 1]['adet'] += 1;
                        array_push($list[$i - 1]['sayac'], $sayac);
                    } else {
                        array_push($list, array('kod' => $sayac['serviskod'], 'sayac' => array($sayac), 'adet' => $sayac['adet']));
                        $i++;
                    }
                }
            }
        }
        return $list;
    }

    public static function SubeDepoGirisGrupla($serinolar,$uretimyerleri,$serviskodlari,$sayacadlari,$sayaccaplari,$nedenler,$takilmatarihleri,$endeksler){

        $list=$sayaclar=$serinolist=$uretimyerilist=$servisstokkod=$sayacadilist=$sayaccaplist=$nedenlist=$takilmatarihlist=$endekslist=array();
        for($i=0;$i<count($serinolar);$i++)
        {
            $serino=$serinolar[$i];
            $uretimyeri=$uretimyerleri[$i];
            $sayacadi=$sayacadlari[$i];
            $sayaccap=$sayaccaplari[$i];
            $serviskod=$serviskodlari[$i];
            $neden=$nedenler[$i];
            $takilmatarihi=$takilmatarihleri[$i];
            $endeks=str_replace(',','.',$endeksler[$i]);
            if($serino!="")
            {
                array_push($sayaclar,array('serino'=>$serino,'sayacadi'=>$sayacadi,'sayaccap'=>$sayaccap,'serviskod'=>$serviskod
                ,'uretimyeri'=>$uretimyeri,'neden'=>$neden,'takilmatarihi'=>$takilmatarihi,'endeks'=>$endeks,'adet'=>1));
            }
        }

        if(count($sayaclar)>0) {
            foreach ($sayaclar as $key => $row) {
                $serinolist[$key] = $row['serino'];
                $uretimyerilist[$key] = $row['uretimyeri'];
                $sayacadilist[$key] = $row['sayacadi'];
                $sayaccaplist[$key] = $row['sayaccap'];
                $servisstokkod[$key] = $row['serviskod'];
                $nedenlist[$key] = $row['neden'];
                $takilmatarihlist[$key] = $row['takilmatarihi'];
                $endekslist[$key] = $row['endeks'];
            }

            array_multisort($servisstokkod, SORT_ASC, $sayaclar);
            $i = 0;
            foreach ($sayaclar as $sayac) {
                if ($i == 0) {
                    array_push($list, array('kod' => $sayac['serviskod'], 'sayac' => array($sayac), 'adet' => $sayac['adet']));
                    $i++;
                } else {
                    if ($list[$i - 1]['kod'] == $sayac['serviskod']) {
                        $list[$i - 1]['adet'] += 1;
                        array_push($list[$i - 1]['sayac'], $sayac);
                    } else {
                        array_push($list, array('kod' => $sayac['serviskod'], 'sayac' => array($sayac), 'adet' => $sayac['adet']));
                        $i++;
                    }
                }
            }
        }
        return $list;
    }

    public static function NetsisDepoGiris($sayaclar,$gelistarih,$netsiscari_id,$belgeno){
        try {
            $netsiscari = NetsisCari::find($netsiscari_id);
            $vadegunu = is_null($netsiscari->vadegunu) ? 0 : $netsiscari->vadegunu;
            $odemetarih = date('Y-m-d', strtotime($gelistarih . ' + ' . $vadegunu . ' days'));
            $servisyetkili = ServisYetkili::where('kullanici_id', Auth::user()->id)->first();
            if (!$servisyetkili) {
                return array('durum' => '0', 'text' => 'Bu Servis Birimi İçin Depoya Sayaç Ekleme Yetkiniz Yok');
            }
            $kalemadedi = count($sayaclar);
            $fatura = Fatuirs::where('SUBE_KODU', 8)->where('FTIRSIP', '9')->where('FATIRS_NO', BackendController::ReverseTrk($belgeno))
                ->where('CARI_KODU',BackendController::ReverseTrk($netsiscari->carikod))->first();
            if ($fatura)
                return array('durum' => '0', 'text' => 'Bu Fatura Numarası Bu Cari için Sistemde Kayıtlı');
            $fatura = new Fatuirs;
            $fatura->SUBE_KODU = 8;
            $fatura->FATIRS_NO = BackendController::ReverseTrk($belgeno);  //ornek A00000000633911
            $fatura->FTIRSIP = '9';    // 9 AMBAR GİRİŞİ
            $fatura->CARI_KODU = BackendController::ReverseTrk($netsiscari->carikod);
            $fatura->TARIH = $gelistarih;
            $fatura->TIPI = 0;
            $fatura->BRUTTUTAR = 0;
            $fatura->KDV = 0;
            $fatura->GENELTOPLAM = 0; //genel toplam
            $fatura->ACIKLAMA = NULL;
            $fatura->KOD1 = $servisyetkili->ozelkod;
            $fatura->ODEMEGUNU = $vadegunu;
            $fatura->ODEMETARIHI = $odemetarih;
            $fatura->KDV_DAHILMI = 'H';
            $fatura->FATKALEM_ADEDI = $kalemadedi; //kalem adedi
            $fatura->SIPARIS_TEST = $gelistarih;
            $fatura->YEDEK22 = 'A';
            $fatura->YEDEK = 'X';
            $fatura->PLA_KODU = $servisyetkili->plasiyerkod;
            $fatura->DOVIZTIP = 0;
            $fatura->DOVIZTUT = 0;
            $fatura->F_YEDEK4 = 1;
            $fatura->C_YEDEK6 = 'F';
            $fatura->D_YEDEK10 = $gelistarih;
            $fatura->PROJE_KODU = '1';
            $fatura->KAYITYAPANKUL = $servisyetkili->netsiskullanici;
            $fatura->KAYITTARIHI = date('Y-m-d H:i:s');
            $fatura->ISLETME_KODU = 1;
            $fatura->save();
            try {
                $faturaek = new Fatuek;
                $faturaek->SUBE_KODU = 8;
                $faturaek->FKOD = '9';
                $faturaek->FATIRSNO = BackendController::ReverseTrk($belgeno);
                $faturaek->CKOD = BackendController::ReverseTrk($netsiscari->carikod);
                $faturaek->save();
                $i = 1;
                $inckey = array();
                try {
                    foreach ($sayaclar as $sayac) {
                        $faturakalem = new Sthar;
                        $servisstokkod = ServisStokKod::find($sayac['kod']);
                        $faturakalem->STOK_KODU = $servisstokkod->stokkodu;
                        $faturakalem->FISNO = BackendController::ReverseTrk($belgeno);
                        $faturakalem->STHAR_GCMIK = $sayac['adet'];
                        $faturakalem->STHAR_GCKOD = 'G';
                        $faturakalem->STHAR_TARIH = $gelistarih;
                        $faturakalem->STHAR_NF = 0;
                        $faturakalem->STHAR_BF = 0;
                        $faturakalem->STHAR_KDV = 18;
                        $faturakalem->STHAR_DOVTIP = 0;
                        $faturakalem->STHAR_DOVFIAT = 0;
                        $faturakalem->DEPO_KODU = $servisyetkili->depokodu;
                        $faturakalem->STHAR_ACIKLAMA = BackendController::ReverseTrk($netsiscari->carikod);
                        $faturakalem->STHAR_FTIRSIP = '9';
                        $faturakalem->LISTE_FIAT = 0;
                        $faturakalem->STHAR_HTUR = 'A';
                        $faturakalem->STHAR_ODEGUN = $vadegunu;
                        $faturakalem->STHAR_BGTIP = 'I';
                        $faturakalem->STHAR_KOD1 = NULL;
                        $faturakalem->STHAR_KOD2 = 'F';
                        $faturakalem->STHAR_CARIKOD = BackendController::ReverseTrk($netsiscari->carikod);
                        $faturakalem->STHAR_SIP_TURU = 'F';
                        $faturakalem->PLASIYER_KODU = $servisyetkili->plasiyerkod;
                        $faturakalem->SIRA = $i;
                        $faturakalem->STRA_SIPKONT = 0;
                        $faturakalem->IRSALIYE_NO = NULL;
                        $faturakalem->IRSALIYE_TARIH = NULL;
                        $faturakalem->STHAR_TESTAR = NULL;
                        $faturakalem->OLCUBR = 0;
                        $faturakalem->VADE_TARIHI = $odemetarih;
                        $faturakalem->SUBE_KODU = 8;
                        $faturakalem->C_YEDEK6 = 'X';
                        $faturakalem->D_YEDEK10 = $gelistarih;
                        $faturakalem->PROJE_KODU = '1';
                        $faturakalem->DUZELTMETARIHI = date('Y-m-d H:i:s');
                        $faturakalem->STRA_IRSKONT = 0;
                        $faturakalem->save();
                        $id = DB::connection('sqlsrv2')->select("SELECT TOP(1)INCKEYNO FROM tblsthar WHERE SUBE_KODU=8 AND FISNO='" . BackendController::ReverseTrk($belgeno) . "' and STHAR_FTIRSIP='9' and STHAR_CARIKOD='".BackendController::ReverseTrk($netsiscari->carikod)."' ORDER BY INCKEYNO DESC");
                        $inckeyno = ($id[0]->INCKEYNO);
                        if (in_array($inckeyno, $inckey)) { //girilen inckey zaten ekliyse kaydedememiş demektir
                            $fatura->delete();
                            $faturaek->delete();
                            Sthar::where('STHAR_FTIRSIP','9')->where('SUBE_KODU',8)
                                ->where('STHAR_CARIKOD',BackendController::ReverseTrk($netsiscari->carikod))
                                ->where('FISNO',BackendController::ReverseTrk($belgeno))->delete();
                            return array('durum' => '0', 'text' => 'Ambar Girişine Ait Kalemler Kısmı Kaydedilemedi');
                        } else { //kaydetme başarılı
                            array_push($inckey, $inckeyno);
                        }
                    }
                } catch (Exception $e) {
                    Log::error($e);
                    $fatura->delete();
                    $faturaek->delete();
                    Sthar::where('STHAR_FTIRSIP','9')->where('SUBE_KODU',8)
                        ->where('STHAR_CARIKOD',BackendController::ReverseTrk($netsiscari->carikod))
                        ->where('FISNO',BackendController::ReverseTrk($belgeno))->delete();
                    return array('durum' => '0', 'text' => 'Ambar Girişine Ait Kalemler Kısmı Kaydedilemedi');
                }
                return array('durum' => '1', 'fatura' => $fatura, 'faturaek' => $faturaek, 'faturakalemler' => $inckey);
            } catch (Exception $e) {
                Log::error($e);
                $fatura->delete();
                return array('durum'=>'0','text'=>'Ambar Girişine Ait Açıklama Kısmı Kaydedilemedi');
            }
        } catch (Exception $e) {
            Log::error($e);
            return array('durum'=>'0','text'=>'Ambar Girişi Kaydedilemedi');
        }
    }

    public static function NetsisDepoGirisDuzenle($dbname,$sayaclar,$gelistarih,$netsiscari_id,$belgeno){
        try {
            $depogelenler = DepoGelen::where('fisno', $belgeno)->where('db_name', $dbname)->get(); // aynı fatura ile giriş yapılanlar
            foreach ($depogelenler as $depogelen) {
                $depogelen->flag = 0; // flag 0 kalanlar kontrol sonrası silinecek
            }
            $netsiscari = NetsisCari::find($netsiscari_id);
            $vadegunu = is_null($netsiscari->vadegunu) ? 0 : $netsiscari->vadegunu;
            $odemetarih = date('Y-m-d', strtotime($gelistarih . ' + ' . $vadegunu . ' days'));
            $servisyetkili = ServisYetkili::where('kullanici_id', Auth::user()->id)->first();
            if (!$servisyetkili) {
                return array('durum' => '0', 'text' => 'Bu Servis Birimi İçin Depoya Sayaç Ekleme Yetkiniz Yok');
            }
            $kalemadedi = count($sayaclar);
            $allkeys = array();
            $alldbnames = array();
            if ($dbname != 'MANAS' . date('Y')) { //eski depo girişi guncelleniyorsa
                $eklenmedurum = false;
                for ($i=0;$i<count($sayaclar);$i++) {
                    $servisstokkod = ServisStokKod::find($sayaclar[$i]['kod']);
                    foreach ($depogelenler as $depogelen) {
                        if ($depogelen->servisstokkodu == $servisstokkod->stokkodu && $depogelen->flag == 0) {
                            $depogelen->flag = 1;
                            if (intval($depogelen->adet) < $sayaclar[$i]['adet']) { //yeni ekleme olmuş ya da bu koda düzenlenmiş
                                $sayaclar[$i]['adet'] -= intval($depogelen->adet);
                                $eklenmedurum = true;
                            }elseif(intval($depogelen->adet) > $sayaclar[$i]['adet']){ // silinme durumu varsa
                                array_push($allkeys, $depogelen->inckeyno);
                                array_push($alldbnames, $depogelen->db_name);
                                DepoGelen::find($depogelen->id)->update(['adet'=>$sayaclar[$i]['adet']]);
                                $sayaclar[$i]['adet'] -= intval($depogelen->adet);
                            }else{
                                $sayaclar[$i]['adet'] = 0;
                                array_push($allkeys, $depogelen->inckeyno);
                                array_push($alldbnames, $depogelen->db_name);
                            }
                        } else {
                            continue;
                        }
                    }
                    if($sayaclar[$i]['adet']>0)
                        $eklenmedurum = true;
                }
                try {
                    foreach ($depogelenler as $depogelen) {
                        if ($depogelen->flag == 0) { // silinecek varsa
                            $hatirlatmalar = Hatirlatma::where('depogelen_id', $depogelen->id)->get();
                            $servistakipler = ServisTakip::where('depogelen_id', $depogelen->id)->get();
                            $sayacgelenler = SayacGelen::where('depogelen_id', $depogelen->id)->get();
                            foreach ($hatirlatmalar as $hatirlatma) {
                                $hatirlatma->delete();
                            }
                            foreach ($servistakipler as $servistakip) {
                                $servistakip->delete();
                            }
                            foreach ($sayacgelenler as $sayacgelen) {
                                $sayacgelen->delete();
                            }
                            $depogelen->delete();
                        }
                    }
                } catch (Exception $e) {
                    Log::error($e);
                    return array('durum' => '0', 'text' => 'Ambar Girişine Ait Silinen Kalemler Güncellenemedi');
                }
                if($eklenmedurum){
                    try {
                        $faturano = Fatuno::where('SUBE_KODU', 8)->where('SERI', 'Z')->where('TIP', '9')->first();
                        if (!$faturano) {
                            $faturano = new Fatuno;
                            $faturano->SUBE_KODU = 8;
                            $faturano->SERI = 'Z';
                            $faturano->TIP = 9;
                            $faturano->NUMARA = 'Z' . '0';
                            $faturano->save();
                        }
                        $belgeno = BackendController::FaturaNo($faturano->NUMARA, 1);
                        Fatuno::where(['SUBE_KODU'=> 8,'SERI' => 'Z','TIP' => '9'])->update(['NUMARA' => $belgeno]);
                    } catch (Exception $e) {
                        DB::rollBack();
                        Log::error($e);
                        return array('durum' => '0', 'text' => 'Girilen Sayaçlar için Fatura Numarası Alınamadı');
                    }
                    try {
                        $fatura = new Fatuirs;
                        $fatura->SUBE_KODU = 8;
                        $fatura->FATIRS_NO = BackendController::ReverseTrk($belgeno);  //ornek A00000000633911
                        $fatura->FTIRSIP = '9';    // 9 AMBAR GİRİŞİ
                        $fatura->CARI_KODU = BackendController::ReverseTrk($netsiscari->carikod);
                        $fatura->TARIH = date('Y-m-d', strtotime('first day of january this year'));
                        $fatura->TIPI = 0;
                        $fatura->BRUTTUTAR = 0;
                        $fatura->KDV = 0;
                        $fatura->GENELTOPLAM = 0; //genel toplam
                        $fatura->ACIKLAMA = NULL;
                        $fatura->KOD1 = $servisyetkili->ozelkod;
                        $fatura->ODEMEGUNU = $vadegunu;
                        $fatura->ODEMETARIHI = date('Y-m-d', strtotime($fatura->TARIH . ' + ' . $vadegunu . ' days'));;
                        $fatura->KDV_DAHILMI = 'H';
                        $fatura->FATKALEM_ADEDI = $kalemadedi; //kalem adedi
                        $fatura->SIPARIS_TEST = $fatura->TARIH;
                        $fatura->YEDEK22 = 'A';
                        $fatura->YEDEK = 'X';
                        $fatura->PLA_KODU = $servisyetkili->plasiyerkod;
                        $fatura->DOVIZTIP = 0;
                        $fatura->DOVIZTUT = 0;
                        $fatura->F_YEDEK4 = 1;
                        $fatura->C_YEDEK6 = 'F';
                        $fatura->D_YEDEK10 = $fatura->TARIH;
                        $fatura->PROJE_KODU = '1';
                        $fatura->KAYITYAPANKUL = $servisyetkili->netsiskullanici;
                        $fatura->KAYITTARIHI = date('Y-m-d H:i:s');
                        $fatura->ISLETME_KODU = 1;
                        $fatura->save();
                        try {
                            $faturaek = new Fatuek;
                            $faturaek->SUBE_KODU = 8;
                            $faturaek->FKOD = '9';
                            $faturaek->FATIRSNO = BackendController::ReverseTrk($belgeno);
                            $faturaek->CKOD = BackendController::ReverseTrk($netsiscari->carikod);
                            $faturaek->save();
                            $i = 1;
                            $inckey = array();
                            foreach ($sayaclar as $sayac) { // kalan adetler için ekleme yapılacak
                                $servisstokkod = ServisStokKod::find($sayac['kod']);
                                try {
                                    if($sayac['adet']>0){
                                        $faturakalem = new Sthar;
                                        $faturakalem->STOK_KODU = $servisstokkod->stokkodu;
                                        $faturakalem->FISNO = BackendController::ReverseTrk($belgeno);
                                        $faturakalem->STHAR_GCMIK = $sayac['adet'];
                                        $faturakalem->STHAR_GCKOD = 'G';
                                        $faturakalem->STHAR_TARIH = $fatura->TARIH;
                                        $faturakalem->STHAR_NF = 0;
                                        $faturakalem->STHAR_BF = 0;
                                        $faturakalem->STHAR_KDV = 18;
                                        $faturakalem->STHAR_DOVTIP = 0;
                                        $faturakalem->STHAR_DOVFIAT = 0;
                                        $faturakalem->DEPO_KODU = $servisyetkili->depokodu;
                                        $faturakalem->STHAR_ACIKLAMA = BackendController::ReverseTrk($netsiscari->carikod);
                                        $faturakalem->STHAR_FTIRSIP = '9';
                                        $faturakalem->LISTE_FIAT = 0;
                                        $faturakalem->STHAR_HTUR = 'A';
                                        $faturakalem->STHAR_ODEGUN = $vadegunu;
                                        $faturakalem->STHAR_BGTIP = 'I';
                                        $faturakalem->STHAR_KOD1 = NULL;
                                        $faturakalem->STHAR_KOD2 = 'F';
                                        $faturakalem->STHAR_CARIKOD = BackendController::ReverseTrk($netsiscari->carikod);
                                        $faturakalem->STHAR_SIP_TURU = 'F';
                                        $faturakalem->PLASIYER_KODU = $servisyetkili->plasiyerkod;
                                        $faturakalem->SIRA = $i;
                                        $faturakalem->STRA_SIPKONT = 0;
                                        $faturakalem->IRSALIYE_NO = NULL;
                                        $faturakalem->IRSALIYE_TARIH = NULL;
                                        $faturakalem->STHAR_TESTAR = NULL;
                                        $faturakalem->OLCUBR = 0;
                                        $faturakalem->VADE_TARIHI = $fatura->ODEMETARIHI;
                                        $faturakalem->SUBE_KODU = 8;
                                        $faturakalem->C_YEDEK6 = 'X';
                                        $faturakalem->D_YEDEK10 = $fatura->TARIH;;
                                        $faturakalem->PROJE_KODU = '1';
                                        $faturakalem->DUZELTMETARIHI = date('Y-m-d H:i:s');
                                        $faturakalem->STRA_IRSKONT = 0;
                                        $faturakalem->save();
                                        $i++;
                                        $id = DB::connection('sqlsrv2')->select("SELECT TOP(1)INCKEYNO FROM tblsthar WHERE SUBE_KODU=8 AND FISNO='" . BackendController::ReverseTrk($belgeno) . "' and STHAR_FTIRSIP='9' and STHAR_CARIKOD='" . BackendController::ReverseTrk($netsiscari->carikod) . "' ORDER BY INCKEYNO DESC");
                                        $inckeyno = ($id[0]->INCKEYNO);
                                        if (in_array($inckeyno, $inckey)) { //girilen inckey zaten ekliyse kaydedememiş demektir
                                            $fatura->delete();
                                            $faturaek->delete();
                                            foreach ($inckey as $inckey_){
                                                Sthar::find($inckey_)->delete();
                                            }
                                            return array('durum' => '0', 'text' => 'Ambar Girişine Ait Kalemler Kısmı Kaydedilemedi');
                                        } else { //kaydetme başarılı
                                            array_push($inckey, $inckeyno);
                                            array_push($allkeys, $inckeyno);
                                            array_push($alldbnames, 'MANAS' . date('Y'));
                                        }
                                    }
                                } catch (Exception $e) {
                                    Log::error($e);
                                    $fatura->delete();
                                    $faturaek->delete();
                                    foreach ($inckey as $inckey_){
                                        Sthar::find($inckey_)->delete();
                                    }
                                    return array('durum' => '0', 'text' => 'Ambar Girişine Ait Kalemler Kısmı Kaydedilemedi');
                                }
                            }
                        }catch (Exception $e) {
                            Log::error($e);
                            $fatura->delete();
                            return array('durum' => '0', 'text' => 'Ambar Girişine Ait Açıklama Kısmı Kaydedilemedi');
                        }
                    }catch (Exception $e) {
                        Log::error($e);
                        return array('durum'=>'0','text'=>'Ambar Girişi Kaydedilemedi');
                    }
                    return array('durum' => '2', 'fatura' => $fatura, 'faturaek' => $faturaek, 'faturakalemler' => $allkeys,'dbnames'=>$alldbnames);
                }else{
                    return array('durum'=>'3','faturakalemler' => $allkeys,'dbnames'=>$alldbnames);
                }
            }else{
                $fatura = Fatuirs::where('SUBE_KODU',8)->where('FTIRSIP','9')->where('FATIRS_NO',BackendController::ReverseTrk($belgeno))
                    ->where('CARI_KODU',BackendController::ReverseTrk($netsiscari->carikod))->first();
                if ($fatura) {
                    try {
                        $eskifatura = clone $fatura;
                        $eskifaturaek=null;
                        $eskikalemler=array();
                        $inckey = array();
                        $fatura->SUBE_KODU = 8;
                        $fatura->FATIRS_NO = BackendController::ReverseTrk($belgeno);  //ornek A00000000633911
                        $fatura->FTIRSIP = '9';    // 9 AMBAR GİRİŞİ
                        $fatura->CARI_KODU = BackendController::ReverseTrk($netsiscari->carikod);
                        $fatura->TARIH = $gelistarih;
                        $fatura->TIPI = 0;
                        $fatura->BRUTTUTAR = 0;
                        $fatura->KDV = 0;
                        $fatura->GENELTOPLAM = 0; //genel toplam
                        $fatura->ACIKLAMA = NULL;
                        $fatura->KOD1 = $servisyetkili->ozelkod;
                        $fatura->ODEMEGUNU = $vadegunu;
                        $fatura->ODEMETARIHI = $odemetarih;
                        $fatura->KDV_DAHILMI = 'H';
                        $fatura->FATKALEM_ADEDI = $kalemadedi; //kalem adedi
                        $fatura->SIPARIS_TEST = $gelistarih;
                        $fatura->YEDEK22 = 'A';
                        $fatura->YEDEK = 'X';
                        $fatura->PLA_KODU = $servisyetkili->plasiyerkod;
                        $fatura->DOVIZTIP = 0;
                        $fatura->DOVIZTUT = 0;
                        $fatura->F_YEDEK4 = 1;
                        $fatura->C_YEDEK6 = 'F';
                        $fatura->D_YEDEK10 = $gelistarih;
                        $fatura->PROJE_KODU = '1';
                        $fatura->DUZELTMEYAPANKUL = $servisyetkili->netsiskullanici;
                        $fatura->DUZELTMETARIHI = date('Y-m-d H:i:s');
                        $fatura->ISLETME_KODU = 1;
                        $fatura->save();
                        try {
                            $faturaek = Fatuek::where('SUBE_KODU',8)->where('FKOD','9')->where('FATIRSNO',BackendController::ReverseTrk($belgeno))
                                ->where('CKOD',BackendController::ReverseTrk($netsiscari->carikod))->first();
                            $eskifaturaek = clone $faturaek;
                            $faturaek->SUBE_KODU = 8;
                            $faturaek->FKOD = '9';
                            $faturaek->FATIRSNO = BackendController::ReverseTrk($belgeno);
                            $faturaek->CKOD = BackendController::ReverseTrk($netsiscari->carikod);
                            $faturaek->save();
                            $i = 1;
                            foreach ($sayaclar as $sayac) {
                                $flag = 0;
                                $servisstokkod = ServisStokKod::find($sayac['kod']);
                                for ($j = 0; $j < $depogelenler->count(); $j++) {
                                    $depogelen = $depogelenler[$j];
                                    if ($depogelen->servisstokkodu == $servisstokkod->stokkodu && $depogelen->flag == 0) {
                                        try {
                                            $flag = 1;
                                            $depogelen->flag = 1;
                                            $faturakalem = Sthar::find($depogelen->inckeyno);
                                            $eskikalem = clone $faturakalem;
                                            array_push($eskikalemler,$eskikalem);
                                            $faturakalem->STOK_KODU = $servisstokkod->stokkodu;
                                            $faturakalem->FISNO = BackendController::ReverseTrk($belgeno);
                                            $faturakalem->STHAR_GCMIK = $sayac['adet'];
                                            $faturakalem->STHAR_GCKOD = 'G';
                                            $faturakalem->STHAR_TARIH = $gelistarih;
                                            $faturakalem->STHAR_NF = 0;
                                            $faturakalem->STHAR_BF = 0;
                                            $faturakalem->STHAR_KDV = 18;
                                            $faturakalem->STHAR_DOVTIP = 0;
                                            $faturakalem->STHAR_DOVFIAT = 0;
                                            $faturakalem->DEPO_KODU = $servisyetkili->depokodu;
                                            $faturakalem->STHAR_ACIKLAMA = BackendController::ReverseTrk($netsiscari->carikod);
                                            $faturakalem->STHAR_FTIRSIP = '9';
                                            $faturakalem->LISTE_FIAT = 0;
                                            $faturakalem->STHAR_HTUR = 'A';
                                            $faturakalem->STHAR_ODEGUN = $vadegunu;
                                            $faturakalem->STHAR_BGTIP = 'I';
                                            $faturakalem->STHAR_KOD1 = NULL;
                                            $faturakalem->STHAR_KOD2 = 'F';
                                            $faturakalem->STHAR_CARIKOD = BackendController::ReverseTrk($netsiscari->carikod);
                                            $faturakalem->STHAR_SIP_TURU = 'F';
                                            $faturakalem->PLASIYER_KODU = $servisyetkili->plasiyerkod;
                                            $faturakalem->SIRA = $i;
                                            $faturakalem->STRA_SIPKONT = 0;
                                            $faturakalem->IRSALIYE_NO = NULL;
                                            $faturakalem->IRSALIYE_TARIH = NULL;
                                            $faturakalem->STHAR_TESTAR = NULL;
                                            $faturakalem->OLCUBR = 0;
                                            $faturakalem->VADE_TARIHI = $odemetarih;
                                            $faturakalem->SUBE_KODU = 8;
                                            $faturakalem->C_YEDEK6 = 'X';
                                            $faturakalem->D_YEDEK10 = $gelistarih;
                                            $faturakalem->PROJE_KODU = '1';
                                            $faturakalem->DUZELTMETARIHI = date('Y-m-d H:i:s');
                                            $faturakalem->STRA_IRSKONT = 0;
                                            $faturakalem->save();
                                            array_push($allkeys, $depogelen->inckeyno);
                                            array_push($alldbnames, $depogelen->db_name);
                                            $i++;
                                            break;
                                        } catch (Exception $e) {
                                            Log::error($e);
                                            BackendController::FaturaGeriAl($eskifatura,$eskifaturaek,$eskikalemler,$inckey);
                                            return array('durum' => '0', 'text' => 'Ambar Girişine Ait Kalemler Kısmı Kaydedilemedi');
                                        }
                                    } else {
                                        continue;
                                    }
                                }
                                if ($flag == 0) { //yeni eklemeyse
                                    try {
                                        $faturakalem = new Sthar;
                                        $faturakalem->STOK_KODU = $servisstokkod->stokkodu;
                                        $faturakalem->FISNO = BackendController::ReverseTrk($belgeno);
                                        $faturakalem->STHAR_GCMIK = $sayac['adet'];
                                        $faturakalem->STHAR_GCKOD = 'G';
                                        $faturakalem->STHAR_TARIH = $gelistarih;
                                        $faturakalem->STHAR_NF = 0;
                                        $faturakalem->STHAR_BF = 0;
                                        $faturakalem->STHAR_KDV = 18;
                                        $faturakalem->STHAR_DOVTIP = 0;
                                        $faturakalem->STHAR_DOVFIAT = 0;
                                        $faturakalem->DEPO_KODU = $servisyetkili->depokodu;
                                        $faturakalem->STHAR_ACIKLAMA = BackendController::ReverseTrk($netsiscari->carikod);
                                        $faturakalem->STHAR_FTIRSIP = '9';
                                        $faturakalem->LISTE_FIAT = 0;
                                        $faturakalem->STHAR_HTUR = 'A';
                                        $faturakalem->STHAR_ODEGUN = $vadegunu;
                                        $faturakalem->STHAR_BGTIP = 'I';
                                        $faturakalem->STHAR_KOD1 = NULL;
                                        $faturakalem->STHAR_KOD2 = 'F';
                                        $faturakalem->STHAR_CARIKOD = BackendController::ReverseTrk($netsiscari->carikod);
                                        $faturakalem->STHAR_SIP_TURU = 'F';
                                        $faturakalem->PLASIYER_KODU = $servisyetkili->plasiyerkod;
                                        $faturakalem->SIRA = $i;
                                        $faturakalem->STRA_SIPKONT = 0;
                                        $faturakalem->IRSALIYE_NO = NULL;
                                        $faturakalem->IRSALIYE_TARIH = NULL;
                                        $faturakalem->STHAR_TESTAR = NULL;
                                        $faturakalem->OLCUBR = 0;
                                        $faturakalem->VADE_TARIHI = $odemetarih;
                                        $faturakalem->SUBE_KODU = 8;
                                        $faturakalem->C_YEDEK6 = 'X';
                                        $faturakalem->D_YEDEK10 = $gelistarih;
                                        $faturakalem->PROJE_KODU = '1';
                                        $faturakalem->DUZELTMETARIHI = date('Y-m-d H:i:s');
                                        $faturakalem->STRA_IRSKONT = 0;
                                        $faturakalem->save();
                                        $i++;
                                        $id = DB::connection('sqlsrv2')->select("SELECT TOP(1)INCKEYNO FROM tblsthar WHERE SUBE_KODU=8 AND FISNO='" . BackendController::ReverseTrk($belgeno) . "' and STHAR_FTIRSIP='9' and STHAR_CARIKOD='".BackendController::ReverseTrk($netsiscari->carikod)."' ORDER BY INCKEYNO DESC");
                                        $inckeyno = ($id[0]->INCKEYNO);
                                        if (in_array($inckeyno, $inckey)) { //girilen inckey zaten ekliyse kaydedememiş demektir
                                            BackendController::FaturaGeriAl($eskifatura,$eskifaturaek,$eskikalemler,$inckey);
                                            return array('durum' => '0', 'text' => 'Ambar Girişine Ait Kalemler Kısmı Kaydedilemedi');
                                        } else { //kaydetme başarılı
                                            array_push($inckey, $inckeyno);
                                            array_push($allkeys, $inckeyno);
                                            array_push($alldbnames, $dbname);
                                        }
                                    } catch (Exception $e) {
                                        Log::error($e);
                                        BackendController::FaturaGeriAl($eskifatura,$eskifaturaek,$eskikalemler,$inckey);
                                        return array('durum' => '0', 'text' => 'Ambar Girişine Ait Kalemler Kısmı Kaydedilemedi');}
                                }
                            }
                            try {
                                foreach ($depogelenler as $depogelen) {
                                    if ($depogelen->flag == 0) { // silinecek varsa
                                        $faturakalem = Sthar::find($depogelen->inckeyno);
                                        if($faturakalem)
                                            $faturakalem->delete();
                                        $hatirlatmalar = Hatirlatma::where('depogelen_id', $depogelen->id)->get();
                                        $servistakipler = ServisTakip::where('depogelen_id', $depogelen->id)->get();
                                        $sayacgelenler = SayacGelen::where('depogelen_id', $depogelen->id)->get();
                                        foreach ($hatirlatmalar as $hatirlatma) {
                                            $hatirlatma->delete();
                                        }
                                        foreach ($servistakipler as $servistakip) {
                                            $servistakip->delete();
                                        }
                                        foreach ($sayacgelenler as $sayacgelen) {
                                            $sayacgelen->delete();
                                        }
                                        $depogelen->delete();
                                    }
                                }
                            } catch (Exception $e) {
                                Log::error($e);
                                BackendController::FaturaGeriAl($eskifatura,$eskifaturaek,$eskikalemler,$inckey);
                                return array('durum' => '0', 'text' => 'Ambar Girişine Ait Silinen Kalemler Güncellenemedi');
                            }
                            return array('durum' => '1', 'fatura' => $fatura, 'faturaek' => $faturaek, 'faturakalemler' => $allkeys,'dbnames'=>$alldbnames);
                        } catch (Exception $e) {
                            Log::error($e);
                            BackendController::FaturaGeriAl($eskifatura,$eskifaturaek,$eskikalemler,$inckey);
                            return array('durum' => '0', 'text' => 'Ambar Girişine Ait Açıklama Kısmı Kaydedilemedi');
                        }
                    } catch (Exception $e) {
                        Log::error($e);
                        return array('durum'=>'0','text'=>'Ambar Girişi Kaydedilemedi');
                    }
                }else{
                    return array('durum'=>'0','text'=>'Fatura bulunamadı! Eski yıllara ait olabilir.');
                }
            }
        } catch (Exception $e) {
            Log::error($e);
            return array('durum'=>'0','text'=>str_replace("'","\'",$e->getMessage()));
        }
    }

    public static function FaturaGeriAl($eskifatura,$eskifaturaek,$eskikalemler,$inckey){
        try {
            $fatura = Fatuirs::where('SUBE_KODU', 8)->where('FTIRSIP', '9')
                ->where('FATIRS_NO', $eskifatura->FATIRS_NO)
                ->where('CARI_KODU', $eskifatura->CARI_KODU)->first();
            if ($fatura) {
                $fatura->SUBE_KODU = 8;
                $fatura->FATIRS_NO = $eskifatura->FATIRS_NO;  //ornek A00000000633911
                $fatura->FTIRSIP = '9';    // 9 AMBAR GİRİŞİ
                $fatura->CARI_KODU = $eskifatura->CARI_KODU;
                $fatura->TARIH = $eskifatura->TARIH;
                $fatura->TIPI = 0;
                $fatura->BRUTTUTAR = 0;
                $fatura->KDV = 0;
                $fatura->GENELTOPLAM = 0; //genel toplam
                $fatura->ACIKLAMA = NULL;
                $fatura->KOD1 = $eskifatura->KOD1;
                $fatura->ODEMEGUNU = $eskifatura->ODEMEGUNU;
                $fatura->ODEMETARIHI = $eskifatura->ODEMETARIHI;
                $fatura->KDV_DAHILMI = 'H';
                $fatura->FATKALEM_ADEDI = $eskifatura->FATKALEM_ADEDI; //kalem adedi
                $fatura->SIPARIS_TEST = $eskifatura->SIPARIS_TEST;
                $fatura->YEDEK22 = 'A';
                $fatura->YEDEK = 'X';
                $fatura->PLA_KODU = $eskifatura->PLA_KODU;
                $fatura->DOVIZTIP = 0;
                $fatura->DOVIZTUT = 0;
                $fatura->F_YEDEK4 = 1;
                $fatura->C_YEDEK6 = 'F';
                $fatura->D_YEDEK10 = $eskifatura->DYEDEK10;
                $fatura->PROJE_KODU = '1';
                $fatura->DUZELTMEYAPANKUL = $eskifatura->DUZELTMEYAPANKUL;
                $fatura->DUZELTMETARIHI = $eskifatura->DUZELTMETARIHI;
                $fatura->ISLETME_KODU = 1;
                $fatura->save();
            }
            $faturaek = Fatuek::where('SUBE_KODU', 8)->where('FKOD', '9')
                ->where('FATIRSNO', $eskifatura->FATIRS_NO)
                ->where('CKOD', $eskifatura->CARI_KODU)->first();
            if ($faturaek) {
                $faturaek->SUBE_KODU = 8;
                $faturaek->FKOD = '9';
                $faturaek->FATIRSNO = $eskifaturaek->FATIRSNO;
                $faturaek->CKOD = $eskifaturaek->CKOD;
                $faturaek->save();
            }
            foreach ($eskikalemler as $eskikalem) {
                $faturakalem = Sthar::find($eskikalem->INCKEYNO);
                if($faturakalem){
                    $faturakalem->STOK_KODU = $eskikalem->STOK_KODU;
                    $faturakalem->FISNO = $eskifatura->FATIRS_NO;
                    $faturakalem->STHAR_GCMIK = $eskikalem->STHAR_GCMIK;
                    $faturakalem->STHAR_GCKOD = 'G';
                    $faturakalem->STHAR_TARIH = $eskikalem->STHAR_TARIH;
                    $faturakalem->STHAR_NF = 0;
                    $faturakalem->STHAR_BF = 0;
                    $faturakalem->STHAR_KDV = 18;
                    $faturakalem->STHAR_DOVTIP = 0;
                    $faturakalem->STHAR_DOVFIAT = 0;
                    $faturakalem->DEPO_KODU = $eskikalem->DEPO_KODU;
                    $faturakalem->STHAR_ACIKLAMA = $eskikalem->STHAR_ACIKLAMA;
                    $faturakalem->STHAR_FTIRSIP = '9';
                    $faturakalem->LISTE_FIAT = 0;
                    $faturakalem->STHAR_HTUR = 'A';
                    $faturakalem->STHAR_ODEGUN = $eskikalem->STHAR_ODEGUN;;
                    $faturakalem->STHAR_BGTIP = 'I';
                    $faturakalem->STHAR_KOD1 = NULL;
                    $faturakalem->STHAR_KOD2 = 'F';
                    $faturakalem->STHAR_CARIKOD = $eskikalem->STHAR_CARIKOD;
                    $faturakalem->STHAR_SIP_TURU = 'F';
                    $faturakalem->PLASIYER_KODU = $eskikalem->PLASIYER_KODU;
                    $faturakalem->SIRA = $eskikalem->SIRA;
                    $faturakalem->STRA_SIPKONT = 0;
                    $faturakalem->IRSALIYE_NO = NULL;
                    $faturakalem->IRSALIYE_TARIH = NULL;
                    $faturakalem->STHAR_TESTAR = NULL;
                    $faturakalem->OLCUBR = 0;
                    $faturakalem->VADE_TARIHI = $eskikalem->VADE_TARIHI;
                    $faturakalem->SUBE_KODU = 8;
                    $faturakalem->C_YEDEK6 = 'X';
                    $faturakalem->D_YEDEK10 = $eskikalem->D_YEDEK10;
                    $faturakalem->PROJE_KODU = '1';
                    $faturakalem->DUZELTMETARIHI = $eskikalem->DUZELTMETARIHI;
                    $faturakalem->STRA_IRSKONT = 0;
                    $faturakalem->save();
                }
            }
            foreach ($inckey as $inckey_) {
                Sthar::find($inckey_)->delete();
            }
            return array('durum'=>'1');
        } catch (Exception $e) {
            Log::error($e);
            return array('durum'=>'0','text'=>str_replace("'","\'",$e->getMessage()));
        }
    }

    public static function SayacGrupla($arizafiyatlist){
        $servisstokkod=array();
        foreach ($arizafiyatlist as $key => $row) {
            $servisstokkod[$key] = $row['servisstokkodu'];
        }

        array_multisort($servisstokkod, SORT_ASC, $arizafiyatlist);
        $i=0;
        $list=array();
        $serinolar="";
        foreach($arizafiyatlist as $arizafiyat)
        {
            if($i==0)
            {
                array_push($list,$arizafiyat);
                $serinolar.=($serinolar=="" ? "" : "-").$arizafiyat['ariza_serino'];
                $i++;
            }else{
                if($list[$i-1]['servisstokkodu']==$arizafiyat['servisstokkodu']) {
                     $list[$i - 1]['adet'] += 1;
                } else {
                     array_push($list, $arizafiyat);
                     $i++;
                }
                $serinolar.=($serinolar=="" ? "" : "-").$arizafiyat['ariza_serino'];
            }
        }
        return array("list"=>$list,"serino"=>$serinolar);
    }

    public static function Listedemi($degisenid,$degisenlist){
        foreach($degisenlist as $degisen)
            if($degisenid==$degisen)
                return true;
        return false;
    }

    public static function FiyatGuncelle($parcaucret){
        if($parcaucret->uretimyer_id==0) //genel fiyat guncellenecek
        {
            $arizafiyatlar=ArizaFiyat::whereIn('durum',array(0,2))->get();
            foreach($arizafiyatlar as $arizafiyat)
            {
                $fiyatlar="";
                $birimler="";
                $total = 0;
                $total2 = 0;
                $garanti=$arizafiyat->ariza_garanti;
                $degisenlist=explode(',',$arizafiyat->degisenler);
                $ucretsizlist=explode(',',$arizafiyat->ucretsiz);
                $genelbirim = BackendController::getGenelParabirimi();
                $ozelbirim = BackendController::getOzelParabirimi($arizafiyat->uretimyer_id);
                $parabirimi = $genelbirim->id;
                $parabirimi2 = null;
                $durum=BackendController::Listedemi($parcaucret->degisenler_id,$degisenlist);
                if($durum){
                    $fiyat=Fiyat::whereIn('degisenler_id',$degisenlist)->where('uretimyer_id',0)->orderBy('degisenler_id','asc')->get()->toArray();
                    if(count($fiyat)==count($degisenlist)) // parçaların genel fiyatları varsa
                    {
                        for($i=0;$i<count($fiyat);$i++){
                            if($ucretsizlist[$i]==0) {
                                if($parabirimi==$fiyat[$i]['parabirimi_id']){
                                    $total += $fiyat[$i]['fiyat'];
                                }else if($parabirimi2==null || $parabirimi2==$fiyat[$i]['parabirimi_id']){
                                    $total2 += $fiyat[$i]['fiyat'];
                                    $parabirimi2 = $fiyat[$i]['parabirimi_id'];
                                }else{
                                    return array('durum'=>false,'error'=>'İki Para Biriminden Fazla Para Birimi Kullanılamaz!');
                                }
                            }
                            $fiyatlar.=($fiyatlar=="" ? '' : ';').$fiyat[$i]['fiyat'];
                            $birimler.=($birimler=="" ? '' : ',').$fiyat[$i]['parabirimi_id'];
                        }
                    }else{ //genel fiyatların tamamı sağlamıyorsa
                        for($i=0;$i<count($degisenlist);$i++)
                        {
                            $flag = 0;
                            for($j=0;$j<count($fiyat);$j++)
                            {
                                if($degisenlist[$i]==$fiyat[$j]['degisenler_id']){
                                    if($ucretsizlist[$i]==0) {
                                        if($parabirimi==$fiyat[$j]['parabirimi_id']){
                                            $total += $fiyat[$j]['fiyat'];
                                        }else if($parabirimi2==null || $parabirimi2==$fiyat[$j]['parabirimi_id']){
                                            $total2 += $fiyat[$j]['fiyat'];
                                            $parabirimi2 = $fiyat[$j]['parabirimi_id'];
                                        }else{
                                            return array('durum'=>false,'error'=>'İki Para Biriminden Fazla Para Birimi Kullanılamaz!');
                                        }
                                    }
                                    $fiyatlar.=($fiyatlar=="" ? '' : ';').$fiyat[$j]['fiyat'];
                                    $birimler.=($birimler=="" ? '' : ',').$fiyat[$j]['parabirimi_id'];
                                    $flag = 1;
                                }
                            }
                            if(!$flag){
                                $fiyatlar.=($fiyatlar=="" ? '' : ';').'0.00';
                                $birimler.=($birimler=="" ? '' : ',').$parabirimi;
                            }
                        }
                    }
                    $arizafiyat->genel=$fiyatlar;
                    $arizafiyat->genelbirim=$birimler;
                    if(!$arizafiyat->fiyatdurum){ //özel fiyatlar geçerli olmadığında
                        if($garanti){
                            $arizafiyat->fiyat=0;
                            $arizafiyat->fiyat2=0;
                        }else{
                            if($arizafiyat->kurtarihi==null) {
                                $arizafiyat->fiyat=$total;
                                $arizafiyat->fiyat2=$total2;
                                $arizafiyat->parabirimi_id=$parabirimi;
                                $arizafiyat->parabirimi2_id=$parabirimi2;
                            }else {
                                if($ozelbirim->id==$genelbirim->id){
                                    $arizafiyat->fiyat=$total;
                                    $arizafiyat->fiyat2=$total2;
                                    $arizafiyat->parabirimi_id=$parabirimi;
                                    $arizafiyat->parabirimi2_id=$parabirimi2;
                                }else{
                                    if($ozelbirim->id==1){ //tl
                                        $kur=BackendController::getKurBilgisi($genelbirim->id,$arizafiyat->kurtarihi);
                                    }else{ //euro dolar sterlin
                                        if($genelbirim->id==1){
                                            $kur=1/BackendController::getKurBilgisi($ozelbirim->id,$arizafiyat->kurtarihi);
                                        }else{
                                            $kur=BackendController::getKurBilgisi($genelbirim->id,$arizafiyat->kurtarihi) / BackendController::getKurBilgisi($ozelbirim->id,$arizafiyat->kurtarihi);
                                        }
                                    }
                                    $arizafiyat->fiyat = $total*$kur;
                                    if($parabirimi2==$ozelbirim->id){
                                        $arizafiyat->fiyat += $total2;
                                        $arizafiyat->fiyat2= 0;
                                        $arizafiyat->parabirimi_id=$ozelbirim->id;
                                        $arizafiyat->parabirimi2_id=null;
                                    }else{
                                        $arizafiyat->fiyat2= $total2;
                                        $arizafiyat->parabirimi_id=$parabirimi;
                                        $arizafiyat->parabirimi2_id=$parabirimi2;
                                    }
                                }
                            }
                        }
                        $indirim = $arizafiyat->indirim==1 ? ($arizafiyat->fiyat*$arizafiyat->indirimorani)/100 : 0;
                        $indirim2 = $arizafiyat->indirim==1 ? ($arizafiyat->fiyat2*$arizafiyat->indirimorani)/100 : 0;
                        $arizafiyat->tutar = $arizafiyat->fiyat - $indirim;
                        $arizafiyat->tutar2 = $arizafiyat->fiyat2 - $indirim2;
                        $arizafiyat->kdv = ($arizafiyat->tutar*18)/100;
                        $arizafiyat->kdv2 = ($arizafiyat->tutar2*18)/100;
                        $arizafiyat->toplamtutar = round(($arizafiyat->tutar + $arizafiyat->kdv) * 2) / 2;
                        $arizafiyat->toplamtutar2 = round(($arizafiyat->tutar2 + $arizafiyat->kdv2) * 2) / 2;
                    }
                    $arizafiyat->save();
                }
            }
        }else{ //ozel fiyat güncellenecek
            $arizafiyatlar=ArizaFiyat::whereIn('durum',array(0,2))->where('uretimyer_id',$parcaucret->uretimyer_id)->get();
            foreach($arizafiyatlar as $arizafiyat)
            {
                $fiyatlar="";
                $birimler="";
                $total = 0;
                $total2 = 0;
                $garanti=$arizafiyat->ariza_garanti;
                $degisenlist=explode(',',$arizafiyat->degisenler);
                $ucretsizlist=explode(',',$arizafiyat->ucretsiz);
                $ozelbirim = BackendController::getOzelParabirimi($arizafiyat->uretimyer_id);
                $parabirimi = $ozelbirim->id;
                $parabirimi2 = null;
                $durum=BackendController::Listedemi($parcaucret->degisenler_id,$degisenlist);
                if($durum){
                    $fiyat=Fiyat::whereIn('degisenler_id',$degisenlist)->where('uretimyer_id',$parcaucret->uretimyer_id)->orderBy('degisenler_id','asc')->get()->toArray();
                    if(count($fiyat)==count($degisenlist)) // parçaların tüm fiyatları varsa
                    {
                        for($i=0;$i<count($fiyat);$i++) {
                            if ($ucretsizlist[$i] == 0) {
                                if ($parabirimi == $fiyat[$i]['parabirimi_id']) {
                                    $total += $fiyat[$i]['fiyat'];
                                } else if ($parabirimi2 == null || $parabirimi2 == $fiyat[$i]['parabirimi_id']) {
                                    $total2 += $fiyat[$i]['fiyat'];
                                    $parabirimi2 = $fiyat[$i]['parabirimi_id'];
                                } else {
                                    return array('durum' => false, 'error' => 'İki Para Biriminden Fazla Para Birimi Kullanılamaz!');
                                }
                            }
                            $fiyatlar .= ($fiyatlar == "" ? '' : ';') . $fiyat[$i]['fiyat'];
                            $birimler .= ($birimler == "" ? '' : ',') . $fiyat[$i]['parabirimi_id'];
                        }
                    }else{ //özel fiyatların tamamı sağlamıyorsa
                        for($i=0;$i<count($degisenlist);$i++)
                        {
                            $flag = 0;
                            for($j=0;$j<count($fiyat);$j++)
                            {
                                if($degisenlist[$i]==$fiyat[$j]['degisenler_id']){
                                    if($ucretsizlist[$i]==0) {
                                        if($parabirimi==$fiyat[$j]['parabirimi_id']){
                                            $total += $fiyat[$j]['fiyat'];
                                        }else if($parabirimi2==null || $parabirimi2==$fiyat[$j]['parabirimi_id']){
                                            $total2 += $fiyat[$j]['fiyat'];
                                            $parabirimi2 = $fiyat[$j]['parabirimi_id'];
                                        }else{
                                            return array('durum'=>false,'error'=>'İki Para Biriminden Fazla Para Birimi Kullanılamaz!');
                                        }
                                    }
                                    $fiyatlar.=($fiyatlar=="" ? '' : ';').$fiyat[$j]['fiyat'];
                                    $birimler.=($birimler=="" ? '' : ',').$fiyat[$j]['parabirimi_id'];
                                    $flag = 1;
                                }
                            }
                            if(!$flag){
                                $fiyatlar.=($fiyatlar=="" ? '' : ';').'0.00';
                                $birimler.=($birimler=="" ? '' : ',').$parabirimi;
                            }
                        }
                    }
                    $arizafiyat->ozel=$fiyatlar;
                    $arizafiyat->ozelbirim=$birimler;
                    if($arizafiyat->fiyatdurum){ // ozel fiyatlandırma geçerli ise
                        if($garanti){
                            $arizafiyat->fiyat=0;
                            $arizafiyat->fiyat2=0;
                        }else {
                            $arizafiyat->fiyat = $total;
                            $arizafiyat->fiyat2 = $total2;
                            $arizafiyat->parabirimi_id = $parabirimi;
                            $arizafiyat->parabirimi2_id = $parabirimi2;
                        }
                        $indirim = $arizafiyat->indirim==1 ? ($arizafiyat->fiyat*$arizafiyat->indirimorani)/100 : 0;
                        $indirim2 = $arizafiyat->indirim==1 ? ($arizafiyat->fiyat2*$arizafiyat->indirimorani)/100 : 0;
                        $arizafiyat->tutar = $arizafiyat->fiyat - $indirim;
                        $arizafiyat->tutar2 = $arizafiyat->fiyat2 - $indirim2;
                        $arizafiyat->kdv = ($arizafiyat->tutar*18)/100;
                        $arizafiyat->kdv2 = ($arizafiyat->tutar2*18)/100;
                        $arizafiyat->toplamtutar = round(($arizafiyat->tutar + $arizafiyat->kdv) * 2) / 2;
                        $arizafiyat->toplamtutar2 = round(($arizafiyat->tutar2 + $arizafiyat->kdv2) * 2) / 2;
                    }
                    $arizafiyat->save();
                }
            }
        }
        return array('durum'=>true,'error'=>'');
    }

    public static function OzelFiyatGuncelle($arizafiyatid)
    {
        try {
            $arizafiyat = ArizaFiyat::find($arizafiyatid);
            if ($arizafiyat->durum == 0 || $arizafiyat->durum == 2) {
                $fiyatlar = "";
                $birimler = "";
                $total = 0;
                $total2 = 0;
                $garanti = $arizafiyat->ariza_garanti;
                $degisenlist = explode(',', $arizafiyat->degisenler);
                $ucretsizlist = explode(',', $arizafiyat->ucretsiz);
                $ozelbirim = BackendController::getOzelParabirimi($arizafiyat->uretimyer_id);
                $parabirimi = $ozelbirim->id;
                $parabirimi2 = null;
                $fiyat = Fiyat::whereIn('degisenler_id', $degisenlist)->where('uretimyer_id', $arizafiyat->uretimyer_id)->orderBy('degisenler_id', 'asc')->get()->toArray();
                if (count($fiyat) == count($degisenlist)) // parçaların tüm fiyatları varsa
                {
                    for ($i = 0; $i < count($fiyat); $i++) {
                        if ($ucretsizlist[$i] == 0) {
                            if ($parabirimi == $fiyat[$i]['parabirimi_id']) {
                                $total += $fiyat[$i]['fiyat'];
                            } else if ($parabirimi2 == null || $parabirimi2 == $fiyat[$i]['parabirimi_id']) {
                                $total2 += $fiyat[$i]['fiyat'];
                                $parabirimi2 = $fiyat[$i]['parabirimi_id'];
                            } else {
                                return array('durum' => false, 'error' => 'İki Para Biriminden Fazla Para Birimi Kullanılamaz!');
                            }
                        }
                        $fiyatlar .= ($fiyatlar == "" ? '' : ';') . $fiyat[$i]['fiyat'];
                        $birimler .= ($birimler == "" ? '' : ',') . $fiyat[$i]['parabirimi_id'];
                    }
                } else { //özel fiyatların tamamı sağlamıyorsa
                    for ($i = 0; $i < count($degisenlist); $i++) {
                        $flag = 0;
                        for ($j = 0; $j < count($fiyat); $j++) {
                            if ($degisenlist[$i] == $fiyat[$j]['degisenler_id']) {
                                if ($ucretsizlist[$i] == 0) {
                                    if ($parabirimi == $fiyat[$j]['parabirimi_id']) {
                                        $total += $fiyat[$j]['fiyat'];
                                    } else if ($parabirimi2 == null || $parabirimi2 == $fiyat[$j]['parabirimi_id']) {
                                        $total2 += $fiyat[$j]['fiyat'];
                                        $parabirimi2 = $fiyat[$j]['parabirimi_id'];
                                    } else {
                                        return array('durum' => false, 'error' => 'İki Para Biriminden Fazla Para Birimi Kullanılamaz!');
                                    }
                                }
                                $fiyatlar .= ($fiyatlar == "" ? '' : ';') . $fiyat[$j]['fiyat'];
                                $birimler .= ($birimler == "" ? '' : ',') . $fiyat[$j]['parabirimi_id'];
                                $flag = 1;
                            }
                        }
                        if (!$flag) {
                            $fiyatlar .= ($fiyatlar == "" ? '' : ';') . '0.00';
                            $birimler .= ($birimler == "" ? '' : ',') . $parabirimi;
                        }
                    }
                }
                $arizafiyat->ozel = $fiyatlar;
                $arizafiyat->ozelbirim = $birimler;
                if ($arizafiyat->fiyatdurum) { // ozel fiyatlandırma geçerli ise
                    if ($garanti) {
                        $arizafiyat->fiyat = 0;
                        $arizafiyat->fiyat2 = 0;
                    } else {
                        $arizafiyat->fiyat = $total;
                        $arizafiyat->fiyat2 = $total2;
                        $arizafiyat->parabirimi_id = $parabirimi;
                        $arizafiyat->parabirimi2_id = $parabirimi2;
                    }
                    $indirim = $arizafiyat->indirim == 1 ? ($arizafiyat->fiyat * $arizafiyat->indirimorani) / 100 : 0;
                    $indirim2 = $arizafiyat->indirim == 1 ? ($arizafiyat->fiyat2 * $arizafiyat->indirimorani) / 100 : 0;
                    $arizafiyat->tutar = $arizafiyat->fiyat - $indirim;
                    $arizafiyat->tutar2 = $arizafiyat->fiyat2 - $indirim2;
                    $arizafiyat->kdv = ($arizafiyat->tutar * 18) / 100;
                    $arizafiyat->kdv2 = ($arizafiyat->tutar2 * 18) / 100;
                    $arizafiyat->toplamtutar = round(($arizafiyat->tutar + $arizafiyat->kdv) * 2) / 2;
                    $arizafiyat->toplamtutar2 = round(($arizafiyat->tutar2 + $arizafiyat->kdv2) * 2) / 2;
                }
                $arizafiyat->save();
            }
            return array('durum' => true, 'error' => '');
        } catch (Exception $e) {
            return array('durum' => false, 'error' => str_replace("'","\'",$e->getMessage()));
        }
    }

    public static function HissedilenSicaklik($sicaklik,$nem)
    {
        $kelvin = $sicaklik + 273;
        $ets = pow(10, ((-2937.4 / $kelvin) - 4.9283 * log($kelvin) / log(10) + 23.5471));
        $etd = $ets * $nem / 100;
        $hissedilen = round($sicaklik + (($etd - 10) * 5 / 9));
        if ($hissedilen < $sicaklik) {
            $hissedilen = $sicaklik;
        }
        return $hissedilen;
    }

    public static function getSayacAdilistesi($kalibrasyongrupid)
    {
        $sayaclist=array();
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $kalibrasyonlar=Kalibrasyon::where('kalibrasyongrup_id',$kalibrasyongrupid)->groupBy('sayacadi_id')->get(array('sayacadi_id'));
        foreach($kalibrasyonlar as $kalibrasyon)
        {
            array_push($sayaclist,$kalibrasyon->sayacadi_id);
        }
        return $sayaclist;
    }

    public static function getSayacTipleri($liste){
        $list=explode(',',$liste);
        $tipler="";
        $sayactipleri=SayacTip::whereIn('id',$list)->get();
        foreach($sayactipleri as $sayactip)
        {
            $sayacmarka=SayacMarka::find($sayactip->sayacmarka_id);
            $tipler.=($tipler=="" ? "" : ", ").$sayacmarka->marka.' '.$sayactip->tipadi;
        }
        return $tipler;
    }

    public static function getSayacAdlari($liste){
        $list=explode(',',$liste);
        $adlar="";
        $sayacadlari=SayacAdi::whereIn('id',$list)->get();
        foreach($sayacadlari as $sayacadi)
        {
            $adlar.=($adlar=="" ? "" : ", ").$sayacadi->sayacadi;
        }
        return $adlar;
    }

    public static function Fiyatlandir($degisenler,$uretimyerid)
    {
        $total = 0;
        $total2 = 0;
        $ozel='';
        $genel='';
        $ozelbirimler='';
        $genelbirimler='';
        $ozelbirim=BackendController::getOzelParabirimi($uretimyerid);
        $genelbirim=BackendController::getGenelParabirimi();
        $parabirimi = $ozelbirim->id;
        $parabirimi2 = null;
        $ozelfiyatlar=Fiyat::whereIn('degisenler_id',$degisenler)->where('uretimyer_id',$uretimyerid)->orderBy('degisenler_id','asc')->get();
        if($ozelfiyatlar->count()==count($degisenler)) // parçaların özel fiyatları varsa
        {
            $durum = 1;
            foreach($ozelfiyatlar as $fiyat) {
                if ($parabirimi == $fiyat->parabirimi_id) {
                    $total += $fiyat->fiyat;
                } else if ($parabirimi2 == null || $parabirimi2 == $fiyat->parabirimi_id) {
                    $total2 += $fiyat->fiyat;
                    $parabirimi2 = $fiyat->parabirimi_id;
                } else {
                    return array('durum' => false, 'error' => 'İki Para Biriminden Fazla Para Birimi Kullanılamaz!');
                }
                $ozel .= ($ozel == "" ? '' : ';') .number_format($fiyat->fiyat,2,'.','');
                $ozelbirimler .= ($ozelbirimler == "" ? '' : ',') . $fiyat->parabirimi_id;
            }
        }else{ //özel fiyatlar tamamını sağlamıyorsa
            $durum = 0;
            foreach($degisenler as $degisen)
            {
                $flag = 0;
                foreach($ozelfiyatlar as $fiyat)
                {
                    if($degisen==$fiyat->degisenler_id){
                        $ozel .= ($ozel == "" ? '' : ';') .number_format($fiyat->fiyat,2,'.','');
                        $ozelbirimler .= ($ozelbirimler == "" ? '' : ',') . $fiyat->parabirimi_id;
                        $flag = 1;
                    }
                }
                if(!$flag){
                    $ozel .= ($ozel == "" ? '' : ';') .'0.00';
                    $ozelbirimler .= ($ozelbirimler == "" ? '' : ',') . $parabirimi;
                }
            }
        }

        //genel fiyatlar
        $genelfiyatlar=Fiyat::whereIn('degisenler_id',$degisenler)->where('uretimyer_id',0)->orderBy('degisenler_id','asc')->get();
        if($durum==0){
            $parabirimi = $genelbirim->id;
            $parabirimi2 = null;
            if($genelfiyatlar->count()==count($degisenler)) // parçaların genel fiyatları varsa
            {
                foreach($genelfiyatlar as $fiyat){
                    if ($parabirimi == $fiyat->parabirimi_id) {
                        $total += $fiyat->fiyat;
                    } else if ($parabirimi2 == null || $parabirimi2 == $fiyat->parabirimi_id) {
                        $total2 += $fiyat->fiyat;
                        $parabirimi2 = $fiyat->parabirimi_id;
                    } else {
                        return array('durum' => false, 'error' => 'İki Para Biriminden Fazla Para Birimi Kullanılamaz!');
                    }
                    $genel .= ($genel == "" ? '' : ';') .number_format($fiyat->fiyat,2,'.','');
                    $genelbirimler .= ($genelbirimler == "" ? '' : ',') . $fiyat->parabirimi_id;
                }
            }else{ //genel fiyatların tamamı sağlamıyorsa
                foreach($degisenler as $degisen)
                {
                    $flag = 0;
                    foreach($genelfiyatlar as $fiyat)
                    {
                        if($degisen==$fiyat->degisenler_id){
                            if ($parabirimi == $fiyat->parabirimi_id) {
                                $total += $fiyat->fiyat;
                            } else if ($parabirimi2 == null || $parabirimi2 == $fiyat->parabirimi_id) {
                                $total2 += $fiyat->fiyat;
                                $parabirimi2 = $fiyat->parabirimi_id;
                            } else {
                                return array('durum' => false, 'error' => 'İki Para Biriminden Fazla Para Birimi Kullanılamaz!');
                            }
                            $genel .= ($genel == "" ? '' : ';') .number_format($fiyat->fiyat,2,'.','');
                            $genelbirimler .= ($genelbirimler == "" ? '' : ',') . $fiyat->parabirimi_id;
                            $flag = 1;
                        }
                    }
                    if(!$flag){
                        $genel .= ($genel == "" ? '' : ';') .'0.00';
                        $genelbirimler .= ($genelbirimler == "" ? '' : ',') . $parabirimi;
                    }
                }
            }
        }else{
            foreach($degisenler as $degisen)
            {
                $flag = 0;
                foreach($genelfiyatlar as $fiyat)
                {
                    if($degisen==$fiyat->degisenler_id){
                        $genel .= ($genel == "" ? '' : ';') .number_format($fiyat->fiyat,2,'.','');
                        $genelbirimler .= ($genelbirimler == "" ? '' : ',') . $fiyat->parabirimi_id;
                        $flag = 1;
                    }
                }
                if(!$flag){
                    $genel .= ($genel == "" ? '' : ';') .'0.00';
                    $genelbirimler .= ($genelbirimler == "" ? '' : ',') . $parabirimi;
                }
            }
        }
        return array('genel'=>$genel,'ozel'=>$ozel,'genelbirimler'=>$genelbirimler,'ozelbirimler'=>$ozelbirimler,'durum'=>$durum,'total'=>$total,'total2'=>$total2,'parabirimi'=>$parabirimi,'parabirimi2'=>$parabirimi2);
    }

    public static function NetsisDepoGirisSil($inckeyno,$fisno,$durum){
        try {
            $servisyetkili = ServisYetkili::where('kullanici_id', Auth::user()->id)->first();
            if (!$servisyetkili) {
                return array('durum' => '0', 'text' => 'Bu Servis Birimi İçin Depodan Sayaç Silme Yetkiniz Yok');
            }
            $faturakalem = Sthar::find($inckeyno);
            if($faturakalem){
                $fatura = Fatuirs::where('SUBE_KODU',$faturakalem->SUBE_KODU)->where('FTIRSIP',$faturakalem->STHAR_FTIRSIP)
                    ->where('FATIRS_NO',$fisno)->first();
                $faturaek = Fatuek::where('SUBE_KODU',$faturakalem->SUBE_KODU)->where('FKOD',$faturakalem->STHAR_FTIRSIP)
                    ->where('FATIRSNO',$fisno)->first();
                if ($fatura) {
                    if ($faturaek) {
                        if ($durum==1) { //sadece fatura kalemi düzenlenecek
                            try {
                                $faturakalem->STHAR_GCMIK -= 1;
                                $faturakalem->save();
                                return array('durum' => '1');
                            } catch (Exception $e) {

                                return array('durum' => '0', 'text' => 'Fatura Kalemi Silinemedi');
                            }
                        } else if ($durum==2) { //sadece fatura kalemi silinecek
                            try {
                                $faturakalem->delete();
                                return array('durum' => '1');
                            } catch (Exception $e) {
                                return array('durum' => '0', 'text' => 'Fatura Kalemi Silinemedi');
                            }
                        } else { //faturaya ait tüm veriler silinecek
                            try {
                                $faturakalem->delete();
                                $faturaek->delete();
                                $fatura->delete();
                                return array('durum' => '1');
                            } catch (Exception $e) {
                                return array('durum' => '0', 'text' => 'Fatura Silinemedi');
                            }
                        }
                    } else {
                        return array('durum' => '0', 'text' => 'Fatura Açıklaması Bulunamadı');
                    }
                } else {
                    return array('durum'=>'0','text'=>'Fatura Bulunamadı');
                }
            }else{
                return array('durum' => '0', 'text' => 'Fatura Kalemi Bulunamadı');
            }
        } catch (Exception $e) {
            Log::error($e);
            return array('durum' => '0', 'text' => str_replace("'","\'",$e->getMessage()));
        }
    }

    public static function SubeDepoGirisSil($subekodu,$inckeyno,$fisno,$durum){
        try {
            $servisyetkili = ServisYetkili::where('kullanici_id', Auth::user()->id)->first();
            if (!$servisyetkili) {
                return array('durum' => '0', 'text' => 'Bu Servis Birimi İçin Depodan Sayaç Silme Yetkiniz Yok');
            }
            $faturakalem = Sthar::find($inckeyno);
            if($faturakalem) {
                $fatura = Fatuirs::where('SUBE_KODU', $faturakalem->SUBE_KODU)->where('FTIRSIP', $faturakalem->STHAR_FTIRSIP)
                    ->where('FATIRS_NO', $fisno)->first();
                $faturaek = Fatuek::where('SUBE_KODU', $faturakalem->SUBE_KODU)->where('FKOD', $faturakalem->STHAR_FTIRSIP)
                    ->where('FATIRSNO', $fisno)->first();
                if ($fatura && $fatura->SUBE_KODU == $subekodu) {
                    if ($faturaek && $faturaek->SUBE_KODU == $subekodu) {
                        if ($faturakalem && $faturakalem->SUBE_KODU == $subekodu) {
                            if ($durum == 1) { //sadece fatura kalemi düzenlenecek
                                try {
                                    $faturakalem->STHAR_GCMIK -= 1;
                                    $faturakalem->save();
                                    return array('durum' => '1');
                                } catch (Exception $e) {
                                    return array('durum' => '0', 'text' => 'Fatura Kalemi Silinemedi');
                                }
                            } else if ($durum == 2) { //sadece fatura kalemi silinecek
                                try {
                                    $faturakalem->delete();
                                    return array('durum' => '1');
                                } catch (Exception $e) {
                                    return array('durum' => '0', 'text' => 'Fatura Kalemi Silinemedi');
                                }
                            } else { //faturaya ait tüm veriler silinecek
                                try {
                                    $faturakalem->delete();
                                    $faturaek->delete();
                                    $fatura->delete();
                                    return array('durum' => '1');
                                } catch (Exception $e) {
                                    return array('durum' => '0', 'text' => 'Fatura Silinemedi');
                                }
                            }
                        } else {
                            return array('durum' => '0', 'text' => 'Fatura Kalemi Bu Şubeye Ait Bulunamadı');
                        }
                    } else {
                        return array('durum' => '0', 'text' => 'Fatura Açıklaması Bulunamadı');
                    }
                } else {
                    return array('durum' => '0', 'text' => 'Fatura Bulunamadı');
                }
            }else{
                return array('durum' => '0', 'text' => 'Fatura Kalemi Bulunamadı');
            }

        } catch (Exception $e) {
            Log::error($e);
            return array('durum' => '0', 'text' => str_replace("'","\'",$e->getMessage()));
        }
    }

    public static function NetsisSubeDepoGiris($sayaclar,$gelistarih,$netsiscari_id,$belgeno){
        try {
            $netsiscari = NetsisCari::find($netsiscari_id);
            $vadegunu = is_null($netsiscari->vadegunu) ? 0 : $netsiscari->vadegunu;
            $odemetarih = date('Y-m-d', strtotime($gelistarih . ' + ' . $vadegunu . ' days'));
            $servisyetkili = ServisYetkili::where('kullanici_id', Auth::user()->id)->first();
            if (!$servisyetkili) {
                return array('durum' => '0', 'text' => 'Bu Servis Birimi İçin Depoya Sayaç Ekleme Yetkiniz Yok');
            }
            $subeyetkili = SubeYetkili::where('kullanici_id', Auth::user()->id)->where('aktif',1)->first();
            $kalemadedi = count($sayaclar);
            $fatura=null;
            $faturaek=null;
            try {
                $fatura = Fatuirs::where('SUBE_KODU', $subeyetkili->subekodu)->where('FTIRSIP', '9')->where('FATIRS_NO', BackendController::ReverseTrk($belgeno))
                    ->where('CARI_KODU',BackendController::ReverseTrk($netsiscari->carikod))->first();
                if ($fatura)
                    return array('durum' => '0', 'text' => 'Bu Fatura Numarası Sistemde Kayıtlı');
                $fatura = new Fatuirs;
                $fatura->SUBE_KODU = $subeyetkili->subekodu;
                $fatura->FATIRS_NO = BackendController::ReverseTrk($belgeno);  //ornek A00000000633911
                $fatura->FTIRSIP = '9';    // 9 AMBAR GİRİŞİ
                $fatura->CARI_KODU = BackendController::ReverseTrk($netsiscari->carikod);
                $fatura->TARIH = $gelistarih;
                $fatura->TIPI = 0;
                $fatura->BRUTTUTAR = 0;
                $fatura->KDV = 0;
                $fatura->GENELTOPLAM = 0; //genel toplam
                $fatura->ACIKLAMA = NULL;
                $fatura->KOD1 = $servisyetkili->ozelkod;
                $fatura->ODEMEGUNU = $vadegunu;
                $fatura->ODEMETARIHI = $odemetarih;
                $fatura->KDV_DAHILMI = 'H';
                $fatura->FATKALEM_ADEDI = $kalemadedi; //kalem adedi
                $fatura->SIPARIS_TEST = $gelistarih;
                $fatura->YEDEK22 = 'A';
                $fatura->YEDEK = 'X';
                $fatura->PLA_KODU = $servisyetkili->plasiyerkod;
                $fatura->DOVIZTIP = 0;
                $fatura->DOVIZTUT = 0;
                $fatura->F_YEDEK4 = 1;
                $fatura->C_YEDEK6 = 'F';
                $fatura->D_YEDEK10 = $gelistarih;
                $fatura->PROJE_KODU = $subeyetkili->projekodu;
                $fatura->KAYITYAPANKUL = $servisyetkili->netsiskullanici;
                $fatura->KAYITTARIHI = date('Y-m-d H:i:s');
                $fatura->ISLETME_KODU = 1;
                $fatura->save();
            } catch (Exception $e) {
                Log::error($e);
                $fatura->delete();
                return array('durum'=>'0','text'=>'Ambar Girişi Kaydedilemedi');
            }
            try {
                $faturaek = new Fatuek;
                $faturaek->SUBE_KODU = $subeyetkili->subekodu;
                $faturaek->FKOD = '9';
                $faturaek->FATIRSNO = BackendController::ReverseTrk($belgeno);
                $faturaek->CKOD = BackendController::ReverseTrk($netsiscari->carikod);
                $faturaek->save();
            } catch (Exception $e) {
                Log::error($e);
                $fatura->delete();
                $faturaek->delete();
                return array('durum'=>'0','text'=>'Ambar Girişine Ait Açıklama Kısmı Kaydedilemedi');
            }
            try {
                $i = 1;
                $inckey = array();
                foreach ($sayaclar as $sayac) {
                    $faturakalem = new Sthar;
                    $servisstokkod = ServisStokKod::find($sayac['kod']);
                    $faturakalem->STOK_KODU = $servisstokkod->stokkodu;
                    $faturakalem->FISNO = BackendController::ReverseTrk($belgeno);
                    $faturakalem->STHAR_GCMIK = $sayac['adet'];
                    $faturakalem->STHAR_GCKOD = 'G';
                    $faturakalem->STHAR_TARIH = $gelistarih;
                    $faturakalem->STHAR_NF = 0;
                    $faturakalem->STHAR_BF = 0;
                    $faturakalem->STHAR_KDV = 18;
                    $faturakalem->STHAR_DOVTIP = 0;
                    $faturakalem->STHAR_DOVFIAT = 0;
                    $faturakalem->DEPO_KODU = $servisyetkili->depokodu;
                    $faturakalem->STHAR_ACIKLAMA = BackendController::ReverseTrk($netsiscari->carikod);
                    $faturakalem->STHAR_FTIRSIP = '9';
                    $faturakalem->LISTE_FIAT = 0;
                    $faturakalem->STHAR_HTUR = 'A';
                    $faturakalem->STHAR_ODEGUN = $vadegunu;
                    $faturakalem->STHAR_BGTIP = 'I';
                    $faturakalem->STHAR_KOD1 = NULL;
                    $faturakalem->STHAR_KOD2 = 'F';
                    $faturakalem->STHAR_CARIKOD = BackendController::ReverseTrk($netsiscari->carikod);
                    $faturakalem->STHAR_SIP_TURU = 'F';
                    $faturakalem->PLASIYER_KODU = $servisyetkili->plasiyerkod;
                    $faturakalem->SIRA = $i;
                    $faturakalem->STRA_SIPKONT = 0;
                    $faturakalem->IRSALIYE_NO = NULL;
                    $faturakalem->IRSALIYE_TARIH = NULL;
                    $faturakalem->STHAR_TESTAR = NULL;
                    $faturakalem->OLCUBR = 0;
                    $faturakalem->VADE_TARIHI = $odemetarih;
                    $faturakalem->SUBE_KODU = $subeyetkili->subekodu;
                    $faturakalem->C_YEDEK6 = 'X';
                    $faturakalem->D_YEDEK10 = $gelistarih;
                    $faturakalem->PROJE_KODU = $subeyetkili->projekodu;
                    $faturakalem->DUZELTMETARIHI = date('Y-m-d H:i:s');
                    $faturakalem->STRA_IRSKONT = 0;
                    $faturakalem->save();
                    $id = DB::connection('sqlsrv2')->select("SELECT TOP(1)INCKEYNO FROM tblsthar WHERE SUBE_KODU=" . $subeyetkili->subekodu . " AND FISNO='" . BackendController::ReverseTrk($belgeno) . "' AND STHAR_FTIRSIP='9' AND STHAR_CARIKOD='".BackendController::ReverseTrk($netsiscari->carikod)."' ORDER BY INCKEYNO DESC");
                    $inckeyno = ($id[0]->INCKEYNO);
                    if (in_array($inckeyno, $inckey)) { //girilen inckey zaten ekliyse kaydedememiş demektir
                        $fatura->delete();
                        $faturaek->delete();
                        Sthar::where('STHAR_FTIRSIP','9')->where('SUBE_KODU',$subeyetkili->subekodu)
                            ->where('STHAR_CARIKOD',BackendController::ReverseTrk($netsiscari->carikod))
                            ->where('FISNO',BackendController::ReverseTrk($belgeno))->delete();
                        return array('durum' => '0', 'text' => 'Ambar Girişine Ait Kalemler Kısmı Kaydedilemedi');
                    } else { //kaydetme başarılı
                        array_push($inckey, $inckeyno);
                    }
                }
            } catch (Exception $e) {
                Log::error($e);
                $fatura->delete();
                $faturaek->delete();
                Sthar::where('STHAR_FTIRSIP','9')->where('SUBE_KODU',$subeyetkili->subekodu)
                    ->where('STHAR_CARIKOD',BackendController::ReverseTrk($netsiscari->carikod))
                    ->where('FISNO',BackendController::ReverseTrk($belgeno))->delete();
                return array('durum' => '0', 'text' => 'Ambar Girişine Ait Kalemler Kısmı Kaydedilemedi');
            }
            return array('durum' => '1', 'fatura' => $fatura, 'faturaek' => $faturaek, 'faturakalemler' => $inckey);

        } catch (Exception $e) {
            Log::error($e);
            return array('durum'=>'0','text'=> str_replace("'","\'",$e->getMessage()));
        }
    }

    public static function SubeDepoGirisDuzenle($dbname,$sayaclar,$gelistarih,$netsiscari_id,$belgeno){
        try {
            $servisyetkili = ServisYetkili::where('kullanici_id', Auth::user()->id)->first();
            if (!$servisyetkili) {
                return array('durum' => '0', 'text' => 'Bu Servis Birimi İçin Depoya Sayaç Ekleme Yetkiniz Yok');
            }
            $subeyetkili = SubeYetkili::where('kullanici_id', Auth::user()->id)->where('aktif',1)->first();
            $depogelenler = DepoGelen::where('fisno', $belgeno)->where('subekodu',$subeyetkili->subekodu)->where('db_name', $dbname)->get(); // aynı fatura ile giriş yapılanlar
            foreach ($depogelenler as $depogelen) {
                $depogelen->flag = 0; // flag 0 kalanlar kontrol sonrası silinecek
            }
            $netsiscari = NetsisCari::find($netsiscari_id);
            $vadegunu = is_null($netsiscari->vadegunu) ? 0 : $netsiscari->vadegunu;
            $odemetarih = date('Y-m-d', strtotime($gelistarih . ' + ' . $vadegunu . ' days'));
            $kalemadedi = count($sayaclar);
            $allkeys = array();
            $alldbnames = array();
            if ($dbname != 'MANAS' . date('Y')) { //eski depo girişi guncelleniyorsa
                $eklenmedurum = false;
                for ($i=0;$i<count($sayaclar);$i++) {
                    $servisstokkod = ServisStokKod::find($sayaclar[$i]['kod']);
                    foreach ($depogelenler as $depogelen) {
                        if ($depogelen->servisstokkodu == $servisstokkod->stokkodu && $depogelen->flag == 0) {
                            $depogelen->flag = 1;
                            if (intval($depogelen->adet) < $sayaclar[$i]['adet']) { //yeni ekleme olmuş ya da bu koda düzenlenmiş
                                $sayaclar[$i]['adet'] -= intval($depogelen->adet);
                                $eklenmedurum = true;
                            }elseif(intval($depogelen->adet) > $sayaclar[$i]['adet']){ // silinme durumu varsa
                                array_push($allkeys, $depogelen->inckeyno);
                                array_push($alldbnames, $depogelen->db_name);
                                DepoGelen::find($depogelen->id)->update(['adet'=>$sayaclar[$i]['adet']]);
                                $sayaclar[$i]['adet'] -= intval($depogelen->adet);
                            }else{
                                $sayaclar[$i]['adet'] = 0;
                                array_push($allkeys, $depogelen->inckeyno);
                                array_push($alldbnames, $depogelen->db_name);
                            }
                        } else {
                            continue;
                        }
                    }
                    if($sayaclar[$i]['adet']>0)
                        $eklenmedurum = true;
                }
                try {
                    foreach ($depogelenler as $depogelen) {
                        if ($depogelen->flag == 0) { // silinecek varsa
                            $hatirlatmalar = Hatirlatma::where('depogelen_id', $depogelen->id)->get();
                            $servistakipler = ServisTakip::where('depogelen_id', $depogelen->id)->get();
                            $sayacgelenler = SayacGelen::where('depogelen_id', $depogelen->id)->get();
                            foreach ($hatirlatmalar as $hatirlatma) {
                                $hatirlatma->delete();
                            }
                            foreach ($servistakipler as $servistakip) {
                                $servistakip->delete();
                            }
                            foreach ($sayacgelenler as $sayacgelen) {
                                $sayacgelen->delete();
                            }
                            $depogelen->delete();
                        }
                    }
                } catch (Exception $e) {
                    Log::error($e);
                    return array('durum' => '0', 'text' => 'Ambar Girişine Ait Silinen Kalemler Güncellenemedi');
                }
                if($eklenmedurum){
                    try {
                        $faturano = Fatuno::where('SUBE_KODU', $subeyetkili->subekodu)->where('SERI', 'Z')->where('TIP', '9')->first();
                        if (!$faturano) {
                            $faturano = new Fatuno;
                            $faturano->SUBE_KODU = $subeyetkili->subekodu;
                            $faturano->SERI = 'Z';
                            $faturano->TIP = 9;
                            $faturano->NUMARA = 'Z' . '0';
                            $faturano->save();
                        }
                        $belgeno = BackendController::FaturaNo($faturano->NUMARA, 1);
                        Fatuno::where(['SUBE_KODU'=> $subeyetkili->subekodu,'SERI' => 'Z','TIP' => '9'])->update(['NUMARA' => $belgeno]);
                    } catch (Exception $e) {
                        DB::rollBack();
                        Log::error($e);
                        return array('durum' => '0', 'text' => 'Girilen Sayaçlar için Fatura Numarası Alınamadı');
                    }
                    try {
                        $fatura = new Fatuirs;
                        $fatura->SUBE_KODU = $subeyetkili->subekodu;
                        $fatura->FATIRS_NO = BackendController::ReverseTrk($belgeno);  //ornek A00000000633911
                        $fatura->FTIRSIP = '9';    // 9 AMBAR GİRİŞİ
                        $fatura->CARI_KODU = BackendController::ReverseTrk($netsiscari->carikod);
                        $fatura->TARIH = date('Y-m-d', strtotime('first day of january this year'));
                        $fatura->TIPI = 0;
                        $fatura->BRUTTUTAR = 0;
                        $fatura->KDV = 0;
                        $fatura->GENELTOPLAM = 0; //genel toplam
                        $fatura->ACIKLAMA = NULL;
                        $fatura->KOD1 = $servisyetkili->ozelkod;
                        $fatura->ODEMEGUNU = $vadegunu;
                        $fatura->ODEMETARIHI = date('Y-m-d', strtotime($fatura->TARIH . ' + ' . $vadegunu . ' days'));;
                        $fatura->KDV_DAHILMI = 'H';
                        $fatura->FATKALEM_ADEDI = $kalemadedi; //kalem adedi
                        $fatura->SIPARIS_TEST = $fatura->TARIH;
                        $fatura->YEDEK22 = 'A';
                        $fatura->YEDEK = 'X';
                        $fatura->PLA_KODU = $servisyetkili->plasiyerkod;
                        $fatura->DOVIZTIP = 0;
                        $fatura->DOVIZTUT = 0;
                        $fatura->F_YEDEK4 = 1;
                        $fatura->C_YEDEK6 = 'F';
                        $fatura->D_YEDEK10 = $fatura->TARIH;
                        $fatura->PROJE_KODU = $subeyetkili->projekodu;
                        $fatura->KAYITYAPANKUL = $servisyetkili->netsiskullanici;
                        $fatura->KAYITTARIHI = date('Y-m-d H:i:s');
                        $fatura->ISLETME_KODU = 1;
                        $fatura->save();
                        try {
                            $faturaek = new Fatuek;
                            $faturaek->SUBE_KODU = $subeyetkili->subekodu;
                            $faturaek->FKOD = '9';
                            $faturaek->FATIRSNO = BackendController::ReverseTrk($belgeno);
                            $faturaek->CKOD = BackendController::ReverseTrk($netsiscari->carikod);
                            $faturaek->save();
                            $i = 1;
                            $inckey = array();
                            foreach ($sayaclar as $sayac) { // kalan adetler için ekleme yapılacak
                                $servisstokkod = ServisStokKod::find($sayac['kod']);
                                try {
                                    if($sayac['adet']>0){
                                        $faturakalem = new Sthar;
                                        $faturakalem->STOK_KODU = $servisstokkod->stokkodu;
                                        $faturakalem->FISNO = BackendController::ReverseTrk($belgeno);
                                        $faturakalem->STHAR_GCMIK = $sayac['adet'];
                                        $faturakalem->STHAR_GCKOD = 'G';
                                        $faturakalem->STHAR_TARIH = $fatura->TARIH;
                                        $faturakalem->STHAR_NF = 0;
                                        $faturakalem->STHAR_BF = 0;
                                        $faturakalem->STHAR_KDV = 18;
                                        $faturakalem->STHAR_DOVTIP = 0;
                                        $faturakalem->STHAR_DOVFIAT = 0;
                                        $faturakalem->DEPO_KODU = $servisyetkili->depokodu;
                                        $faturakalem->STHAR_ACIKLAMA = BackendController::ReverseTrk($netsiscari->carikod);
                                        $faturakalem->STHAR_FTIRSIP = '9';
                                        $faturakalem->LISTE_FIAT = 0;
                                        $faturakalem->STHAR_HTUR = 'A';
                                        $faturakalem->STHAR_ODEGUN = $vadegunu;
                                        $faturakalem->STHAR_BGTIP = 'I';
                                        $faturakalem->STHAR_KOD1 = NULL;
                                        $faturakalem->STHAR_KOD2 = 'F';
                                        $faturakalem->STHAR_CARIKOD = BackendController::ReverseTrk($netsiscari->carikod);
                                        $faturakalem->STHAR_SIP_TURU = 'F';
                                        $faturakalem->PLASIYER_KODU = $servisyetkili->plasiyerkod;
                                        $faturakalem->SIRA = $i;
                                        $faturakalem->STRA_SIPKONT = 0;
                                        $faturakalem->IRSALIYE_NO = NULL;
                                        $faturakalem->IRSALIYE_TARIH = NULL;
                                        $faturakalem->STHAR_TESTAR = NULL;
                                        $faturakalem->OLCUBR = 0;
                                        $faturakalem->VADE_TARIHI = $fatura->ODEMETARIHI;
                                        $faturakalem->SUBE_KODU = $subeyetkili->subekodu;
                                        $faturakalem->C_YEDEK6 = 'X';
                                        $faturakalem->D_YEDEK10 = $fatura->TARIH;;
                                        $faturakalem->PROJE_KODU = $subeyetkili->projekodu;
                                        $faturakalem->DUZELTMETARIHI = date('Y-m-d H:i:s');
                                        $faturakalem->STRA_IRSKONT = 0;
                                        $faturakalem->save();
                                        $i++;
                                        $id = DB::connection('sqlsrv2')->select("SELECT TOP(1)INCKEYNO FROM tblsthar WHERE SUBE_KODU=" . $subeyetkili->subekodu . " AND FISNO='" . BackendController::ReverseTrk($belgeno) . "' and STHAR_FTIRSIP='9' and STHAR_CARIKOD='" . BackendController::ReverseTrk($netsiscari->carikod) . "' ORDER BY INCKEYNO DESC");
                                        $inckeyno = ($id[0]->INCKEYNO);
                                        if (in_array($inckeyno, $inckey)) { //girilen inckey zaten ekliyse kaydedememiş demektir
                                            $fatura->delete();
                                            $faturaek->delete();
                                            foreach ($inckey as $inckey_){
                                                Sthar::find($inckey_)->delete();
                                            }
                                            return array('durum' => '0', 'text' => 'Ambar Girişine Ait Kalemler Kısmı Kaydedilemedi');
                                        } else { //kaydetme başarılı
                                            array_push($inckey, $inckeyno);
                                            array_push($allkeys, $inckeyno);
                                            array_push($alldbnames, 'MANAS' . date('Y'));
                                        }
                                    }
                                } catch (Exception $e) {
                                    Log::error($e);
                                    $fatura->delete();
                                    $faturaek->delete();
                                    foreach ($inckey as $inckey_){
                                        Sthar::find($inckey_)->delete();
                                    }
                                    return array('durum' => '0', 'text' => 'Ambar Girişine Ait Kalemler Kısmı Kaydedilemedi');
                                }
                            }
                        }catch (Exception $e) {
                            Log::error($e);
                            $fatura->delete();
                            return array('durum' => '0', 'text' => 'Ambar Girişine Ait Açıklama Kısmı Kaydedilemedi');
                        }
                    }catch (Exception $e) {
                        Log::error($e);
                        return array('durum'=>'0','text'=>'Ambar Girişi Kaydedilemedi');
                    }
                    return array('durum' => '2', 'fatura' => $fatura, 'faturaek' => $faturaek, 'faturakalemler' => $allkeys,'dbnames'=>$alldbnames);
                }else{
                    return array('durum'=>'3','faturakalemler' => $allkeys,'dbnames'=>$alldbnames);
                }
            }else{
                $fatura = Fatuirs::where('SUBE_KODU', $subeyetkili->subekodu)->where('FTIRSIP','9')->where('FATIRS_NO',BackendController::ReverseTrk($belgeno))
                    ->where('CARI_KODU',BackendController::ReverseTrk($netsiscari->carikod))->first();
                if ($fatura) {
                    try {
                        $eskifatura = clone $fatura;
                        $eskifaturaek=null;
                        $eskikalemler=array();
                        $inckey = array();
                        $fatura->SUBE_KODU = $subeyetkili->subekodu;
                        $fatura->FATIRS_NO = BackendController::ReverseTrk($belgeno);  //ornek A00000000633911
                        $fatura->FTIRSIP = '9';    // 9 AMBAR GİRİŞİ
                        $fatura->CARI_KODU = BackendController::ReverseTrk($netsiscari->carikod);
                        $fatura->TARIH = $gelistarih;
                        $fatura->TIPI = 0;
                        $fatura->BRUTTUTAR = 0;
                        $fatura->KDV = 0;
                        $fatura->GENELTOPLAM = 0; //genel toplam
                        $fatura->ACIKLAMA = NULL;
                        $fatura->KOD1 = $servisyetkili->ozelkod;
                        $fatura->ODEMEGUNU = $vadegunu;
                        $fatura->ODEMETARIHI = $odemetarih;
                        $fatura->KDV_DAHILMI = 'H';
                        $fatura->FATKALEM_ADEDI = $kalemadedi; //kalem adedi
                        $fatura->SIPARIS_TEST = $gelistarih;
                        $fatura->YEDEK22 = 'A';
                        $fatura->YEDEK = 'X';
                        $fatura->PLA_KODU = $servisyetkili->plasiyerkod;
                        $fatura->DOVIZTIP = 0;
                        $fatura->DOVIZTUT = 0;
                        $fatura->F_YEDEK4 = 1;
                        $fatura->C_YEDEK6 = 'F';
                        $fatura->D_YEDEK10 = $gelistarih;
                        $fatura->PROJE_KODU = $subeyetkili->projekodu;
                        $fatura->DUZELTMEYAPANKUL = $servisyetkili->netsiskullanici;
                        $fatura->DUZELTMETARIHI = date('Y-m-d H:i:s');
                        $fatura->ISLETME_KODU = 1;
                        $fatura->save();
                        try {
                            $faturaek = Fatuek::where('SUBE_KODU',$fatura->SUBE_KODU)->where('FKOD',$fatura->FTIRSIP)->where('FATIRSNO',BackendController::ReverseTrk($belgeno))
                                ->where('CKOD',BackendController::ReverseTrk($netsiscari->carikod))->first();
                            $eskifaturaek = clone $faturaek;
                            $faturaek->SUBE_KODU = $subeyetkili->subekodu;
                            $faturaek->FKOD = '9';
                            $faturaek->FATIRSNO = BackendController::ReverseTrk($belgeno);
                            $faturaek->CKOD = BackendController::ReverseTrk($netsiscari->carikod);
                            $faturaek->save();
                            $i = 1;
                            foreach ($sayaclar as $sayac) {
                                $flag = 0;
                                $servisstokkod = ServisStokKod::find($sayac['kod']);
                                for ($j = 0; $j < $depogelenler->count(); $j++) {
                                    $depogelen = $depogelenler[$j];
                                    if ($depogelen->servisstokkodu == $servisstokkod->stokkodu && $depogelen->flag == 0) {
                                        try {
                                            $flag = 1;
                                            $depogelen->flag = 1;
                                            $faturakalem = Sthar::find($depogelen->inckeyno);
                                            $eskikalem = clone $faturakalem;
                                            array_push($eskikalemler,$eskikalem);
                                            $faturakalem->STOK_KODU = $servisstokkod->stokkodu;
                                            $faturakalem->FISNO = BackendController::ReverseTrk($belgeno);
                                            $faturakalem->STHAR_GCMIK = $sayac['adet'];
                                            $faturakalem->STHAR_GCKOD = 'G';
                                            $faturakalem->STHAR_TARIH = $gelistarih;
                                            $faturakalem->STHAR_NF = 0;
                                            $faturakalem->STHAR_BF = 0;
                                            $faturakalem->STHAR_KDV = 18;
                                            $faturakalem->STHAR_DOVTIP = 0;
                                            $faturakalem->STHAR_DOVFIAT = 0;
                                            $faturakalem->DEPO_KODU = $servisyetkili->depokodu;
                                            $faturakalem->STHAR_ACIKLAMA = BackendController::ReverseTrk($netsiscari->carikod);
                                            $faturakalem->STHAR_FTIRSIP = '9';
                                            $faturakalem->LISTE_FIAT = 0;
                                            $faturakalem->STHAR_HTUR = 'A';
                                            $faturakalem->STHAR_ODEGUN = $vadegunu;
                                            $faturakalem->STHAR_BGTIP = 'I';
                                            $faturakalem->STHAR_KOD1 = NULL;
                                            $faturakalem->STHAR_KOD2 = 'F';
                                            $faturakalem->STHAR_CARIKOD = BackendController::ReverseTrk($netsiscari->carikod);
                                            $faturakalem->STHAR_SIP_TURU = 'F';
                                            $faturakalem->PLASIYER_KODU = $servisyetkili->plasiyerkod;
                                            $faturakalem->SIRA = $i;
                                            $faturakalem->STRA_SIPKONT = 0;
                                            $faturakalem->IRSALIYE_NO = NULL;
                                            $faturakalem->IRSALIYE_TARIH = NULL;
                                            $faturakalem->STHAR_TESTAR = NULL;
                                            $faturakalem->OLCUBR = 0;
                                            $faturakalem->VADE_TARIHI = $odemetarih;
                                            $faturakalem->SUBE_KODU = $subeyetkili->subekodu;
                                            $faturakalem->C_YEDEK6 = 'X';
                                            $faturakalem->D_YEDEK10 = $gelistarih;
                                            $faturakalem->PROJE_KODU = $subeyetkili->projekodu;
                                            $faturakalem->DUZELTMETARIHI = date('Y-m-d H:i:s');
                                            $faturakalem->STRA_IRSKONT = 0;
                                            $faturakalem->save();
                                            array_push($allkeys, $depogelen->inckeyno);
                                            array_push($alldbnames, $depogelen->db_name);
                                            $i++;
                                            break;
                                        } catch (Exception $e) {
                                            Log::error($e);
                                            BackendController::SubeFaturaGeriAl($eskifatura,$eskifaturaek,$eskikalemler,$inckey,$subeyetkili->subekodu);
                                            return array('durum' => '0', 'text' => 'Ambar Girişine Ait Kalemler Kısmı Kaydedilemedi');
                                        }
                                    } else {
                                        continue;
                                    }
                                }
                                if ($flag == 0) { //yeni eklemeyse
                                    try {
                                        $faturakalem = new Sthar;
                                        $faturakalem->STOK_KODU = $servisstokkod->stokkodu;
                                        $faturakalem->FISNO = BackendController::ReverseTrk($belgeno);
                                        $faturakalem->STHAR_GCMIK = $sayac['adet'];
                                        $faturakalem->STHAR_GCKOD = 'G';
                                        $faturakalem->STHAR_TARIH = $gelistarih;
                                        $faturakalem->STHAR_NF = 0;
                                        $faturakalem->STHAR_BF = 0;
                                        $faturakalem->STHAR_KDV = 18;
                                        $faturakalem->STHAR_DOVTIP = 0;
                                        $faturakalem->STHAR_DOVFIAT = 0;
                                        $faturakalem->DEPO_KODU = $servisyetkili->depokodu;
                                        $faturakalem->STHAR_ACIKLAMA = BackendController::ReverseTrk($netsiscari->carikod);
                                        $faturakalem->STHAR_FTIRSIP = '9';
                                        $faturakalem->LISTE_FIAT = 0;
                                        $faturakalem->STHAR_HTUR = 'A';
                                        $faturakalem->STHAR_ODEGUN = $vadegunu;
                                        $faturakalem->STHAR_BGTIP = 'I';
                                        $faturakalem->STHAR_KOD1 = NULL;
                                        $faturakalem->STHAR_KOD2 = 'F';
                                        $faturakalem->STHAR_CARIKOD = BackendController::ReverseTrk($netsiscari->carikod);
                                        $faturakalem->STHAR_SIP_TURU = 'F';
                                        $faturakalem->PLASIYER_KODU = $servisyetkili->plasiyerkod;
                                        $faturakalem->SIRA = $i;
                                        $faturakalem->STRA_SIPKONT = 0;
                                        $faturakalem->IRSALIYE_NO = NULL;
                                        $faturakalem->IRSALIYE_TARIH = NULL;
                                        $faturakalem->STHAR_TESTAR = NULL;
                                        $faturakalem->OLCUBR = 0;
                                        $faturakalem->VADE_TARIHI = $odemetarih;
                                        $faturakalem->SUBE_KODU = $subeyetkili->subekodu;
                                        $faturakalem->C_YEDEK6 = 'X';
                                        $faturakalem->D_YEDEK10 = $gelistarih;
                                        $faturakalem->PROJE_KODU = $subeyetkili->projekodu;
                                        $faturakalem->DUZELTMETARIHI = date('Y-m-d H:i:s');
                                        $faturakalem->STRA_IRSKONT = 0;
                                        $faturakalem->save();
                                        $i++;
                                        $id = DB::connection('sqlsrv2')->select("SELECT TOP(1)INCKEYNO FROM tblsthar WHERE SUBE_KODU=" . $subeyetkili->subekodu . " AND FISNO='" . BackendController::ReverseTrk($belgeno) . "' and STHAR_FTIRSIP='9' and STHAR_CARIKOD='".BackendController::ReverseTrk($netsiscari->carikod)."' ORDER BY INCKEYNO DESC");
                                        $inckeyno = ($id[0]->INCKEYNO);
                                        if (in_array($inckeyno, $inckey)) { //girilen inckey zaten ekliyse kaydedememiş demektir
                                            BackendController::SubeFaturaGeriAl($eskifatura,$eskifaturaek,$eskikalemler,$inckey,$subeyetkili->subekodu);
                                            return array('durum' => '0', 'text' => 'Ambar Girişine Ait Kalemler Kısmı Kaydedilemedi');
                                        } else { //kaydetme başarılı
                                            array_push($inckey, $inckeyno);
                                            array_push($allkeys, $inckeyno);
                                            array_push($alldbnames, $dbname);
                                        }
                                    } catch (Exception $e) {
                                        Log::error($e);
                                        BackendController::SubeFaturaGeriAl($eskifatura,$eskifaturaek,$eskikalemler,$inckey,$subeyetkili->subekodu);
                                        return array('durum' => '0', 'text' => 'Ambar Girişine Ait Kalemler Kısmı Kaydedilemedi');}
                                }
                            }
                            try {
                                foreach ($depogelenler as $depogelen) {
                                    if ($depogelen->flag == 0) { // silinecek varsa
                                        $faturakalem = Sthar::find($depogelen->inckeyno);
                                        if($faturakalem)
                                            $faturakalem->delete();
                                        $hatirlatmalar = Hatirlatma::where('depogelen_id', $depogelen->id)->get();
                                        $servistakipler = ServisTakip::where('depogelen_id', $depogelen->id)->get();
                                        $sayacgelenler = SayacGelen::where('depogelen_id', $depogelen->id)->get();
                                        foreach ($hatirlatmalar as $hatirlatma) {
                                            $hatirlatma->delete();
                                        }
                                        foreach ($servistakipler as $servistakip) {
                                            $servistakip->delete();
                                        }
                                        foreach ($sayacgelenler as $sayacgelen) {
                                            $sayacgelen->delete();
                                        }
                                        $depogelen->delete();
                                    }
                                }
                            } catch (Exception $e) {
                                Log::error($e);
                                BackendController::SubeFaturaGeriAl($eskifatura,$eskifaturaek,$eskikalemler,$inckey,$subeyetkili->subekodu);
                                return array('durum' => '0', 'text' => 'Ambar Girişine Ait Silinen Kalemler Güncellenemedi');
                            }
                            return array('durum' => '1', 'fatura' => $fatura, 'faturaek' => $faturaek, 'faturakalemler' => $allkeys,'dbnames'=>$alldbnames);
                        } catch (Exception $e) {
                            Log::error($e);
                            BackendController::SubeFaturaGeriAl($eskifatura,$eskifaturaek,$eskikalemler,$inckey,$subeyetkili->subekodu);
                            return array('durum' => '0', 'text' => 'Ambar Girişine Ait Açıklama Kısmı Kaydedilemedi');
                        }
                    } catch (Exception $e) {
                        Log::error($e);
                        return array('durum'=>'0','text'=>'Ambar Girişi Kaydedilemedi');
                    }
                }else{
                    return array('durum'=>'0','text'=>'Fatura bulunamadı! Eski yıllara ait olabilir.');
                }
            }
        } catch (Exception $e) {
            Log::error($e);
            return array('durum'=>'0','text'=>str_replace("'","\'",$e->getMessage()));
        }
    }

    public static function SubeFaturaGeriAl($eskifatura,$eskifaturaek,$eskikalemler,$inckey,$subekodu){
        try {
            $fatura = Fatuirs::where('SUBE_KODU', $subekodu)->where('FTIRSIP', '9')
                ->where('FATIRS_NO', $eskifatura->FATIRS_NO)
                ->where('CARI_KODU', $eskifatura->CARI_KODU)->first();
            if ($fatura) {
                $fatura->SUBE_KODU = $subekodu;
                $fatura->FATIRS_NO = $eskifatura->FATIRS_NO;  //ornek A00000000633911
                $fatura->FTIRSIP = '9';    // 9 AMBAR GİRİŞİ
                $fatura->CARI_KODU = $eskifatura->CARI_KODU;
                $fatura->TARIH = $eskifatura->TARIH;
                $fatura->TIPI = 0;
                $fatura->BRUTTUTAR = 0;
                $fatura->KDV = 0;
                $fatura->GENELTOPLAM = 0; //genel toplam
                $fatura->ACIKLAMA = NULL;
                $fatura->KOD1 = $eskifatura->KOD1;
                $fatura->ODEMEGUNU = $eskifatura->ODEMEGUNU;
                $fatura->ODEMETARIHI = $eskifatura->ODEMETARIHI;
                $fatura->KDV_DAHILMI = 'H';
                $fatura->FATKALEM_ADEDI = $eskifatura->FATKALEM_ADEDI; //kalem adedi
                $fatura->SIPARIS_TEST = $eskifatura->SIPARIS_TEST;
                $fatura->YEDEK22 = 'A';
                $fatura->YEDEK = 'X';
                $fatura->PLA_KODU = $eskifatura->PLA_KODU;
                $fatura->DOVIZTIP = 0;
                $fatura->DOVIZTUT = 0;
                $fatura->F_YEDEK4 = 1;
                $fatura->C_YEDEK6 = 'F';
                $fatura->D_YEDEK10 = $eskifatura->DYEDEK10;
                $fatura->PROJE_KODU = '1';
                $fatura->DUZELTMEYAPANKUL = $eskifatura->DUZELTMEYAPANKUL;
                $fatura->DUZELTMETARIHI = $eskifatura->DUZELTMETARIHI;
                $fatura->ISLETME_KODU = 1;
                $fatura->save();
            }
            $faturaek = Fatuek::where('SUBE_KODU', $subekodu)->where('FKOD', '9')
                ->where('FATIRSNO', $eskifatura->FATIRS_NO)
                ->where('CKOD', $eskifatura->CARI_KODU)->first();
            if ($faturaek) {
                $faturaek->SUBE_KODU = $subekodu;
                $faturaek->FKOD = '9';
                $faturaek->FATIRSNO = $eskifaturaek->FATIRSNO;
                $faturaek->CKOD = $eskifaturaek->CKOD;
                $faturaek->save();
            }
            foreach ($eskikalemler as $eskikalem) {
                $faturakalem = Sthar::find($eskikalem->INCKEYNO);
                if($faturakalem){
                    $faturakalem->STOK_KODU = $eskikalem->STOK_KODU;
                    $faturakalem->FISNO = $eskifatura->FATIRS_NO;
                    $faturakalem->STHAR_GCMIK = $eskikalem->STHAR_GCMIK;
                    $faturakalem->STHAR_GCKOD = 'G';
                    $faturakalem->STHAR_TARIH = $eskikalem->STHAR_TARIH;
                    $faturakalem->STHAR_NF = 0;
                    $faturakalem->STHAR_BF = 0;
                    $faturakalem->STHAR_KDV = 18;
                    $faturakalem->STHAR_DOVTIP = 0;
                    $faturakalem->STHAR_DOVFIAT = 0;
                    $faturakalem->DEPO_KODU = $eskikalem->DEPO_KODU;
                    $faturakalem->STHAR_ACIKLAMA = $eskikalem->STHAR_ACIKLAMA;
                    $faturakalem->STHAR_FTIRSIP = '9';
                    $faturakalem->LISTE_FIAT = 0;
                    $faturakalem->STHAR_HTUR = 'A';
                    $faturakalem->STHAR_ODEGUN = $eskikalem->STHAR_ODEGUN;;
                    $faturakalem->STHAR_BGTIP = 'I';
                    $faturakalem->STHAR_KOD1 = NULL;
                    $faturakalem->STHAR_KOD2 = 'F';
                    $faturakalem->STHAR_CARIKOD = $eskikalem->STHAR_CARIKOD;
                    $faturakalem->STHAR_SIP_TURU = 'F';
                    $faturakalem->PLASIYER_KODU = $eskikalem->PLASIYER_KODU;
                    $faturakalem->SIRA = $eskikalem->SIRA;
                    $faturakalem->STRA_SIPKONT = 0;
                    $faturakalem->IRSALIYE_NO = NULL;
                    $faturakalem->IRSALIYE_TARIH = NULL;
                    $faturakalem->STHAR_TESTAR = NULL;
                    $faturakalem->OLCUBR = 0;
                    $faturakalem->VADE_TARIHI = $eskikalem->VADE_TARIHI;
                    $faturakalem->SUBE_KODU = $subekodu;
                    $faturakalem->C_YEDEK6 = 'X';
                    $faturakalem->D_YEDEK10 = $eskikalem->D_YEDEK10;
                    $faturakalem->PROJE_KODU = '1';
                    $faturakalem->DUZELTMETARIHI = $eskikalem->DUZELTMETARIHI;
                    $faturakalem->STRA_IRSKONT = 0;
                    $faturakalem->save();
                }
            }
            foreach ($inckey as $inckey_) {
                Sthar::find($inckey_)->delete();
            }
            return array('durum'=>'1');
        } catch (Exception $e) {
            Log::error($e);
            return array('durum'=>'0','text'=>str_replace("'","\'",$e->getMessage()));
        }
    }

    public static function SubeDepoGiris($dbname,$sayaclar,$gelistarih,$netsiscari_id,$subekodu,$belgeno){
        try {
            $servisyetkili = ServisYetkili::where('kullanici_id', Auth::user()->id)->first();
            if (!$servisyetkili) {
                return array('durum' => '0', 'text' => 'Bu Servis Birimi İçin Depoya Sayaç Ekleme Yetkiniz Yok');
            }
            $subeyetkili = SubeYetkili::where('kullanici_id', Auth::user()->id)->where('aktif',1)->first();
            $netsiscari = NetsisCari::find($netsiscari_id);
            $vadegunu = is_null($netsiscari->vadegunu) ? 0 : $netsiscari->vadegunu;
            $odemetarih = date('Y-m-d', strtotime($gelistarih . ' + ' . $vadegunu . ' days'));
            $kalemadedi = count($sayaclar);
            $allkeys = array();
            $alldbnames = array();
            if ($dbname != 'MANAS' . date('Y')) { //eski depo girişi guncelleniyorsa
                $eklenmedurum = false;
                if($belgeno!=null){
                    $depogelenler = DepoGelen::where('fisno', $belgeno)->where('carikod',$netsiscari->carikod)->where('subekodu',$subekodu)->where('db_name', $dbname)->get(); // aynı fatura ile giriş yapılanlar
                    foreach ($depogelenler as $depogelen) {
                        $depogelen->flag = 0; // flag 0 kalanlar kontrol sonrası silinecek
                    }
                    for ($i=0;$i<count($sayaclar);$i++) {
                        $servisstokkod = ServisStokKod::find($sayaclar[$i]['kod']);
                        foreach ($depogelenler as $depogelen) {
                            if ($depogelen->servisstokkodu == $servisstokkod->stokkodu && $depogelen->flag == 0) {
                                $depogelen->flag = 1;
                                if (intval($depogelen->adet) < $sayaclar[$i]['adet']) { //yeni ekleme olmuş ya da bu koda düzenlenmiş
                                    $sayaclar[$i]['adet'] -= intval($depogelen->adet);
                                    $eklenmedurum = true;
                                }elseif(intval($depogelen->adet) > $sayaclar[$i]['adet']){ // silinme durumu varsa
                                    array_push($allkeys, $depogelen->inckeyno);
                                    array_push($alldbnames, $depogelen->db_name);
                                    DepoGelen::find($depogelen->id)->update(['adet'=>$sayaclar[$i]['adet']]);
                                    $sayaclar[$i]['adet'] -= intval($depogelen->adet);
                                }else{
                                    $sayaclar[$i]['adet'] = 0;
                                    array_push($allkeys, $depogelen->inckeyno);
                                    array_push($alldbnames, $depogelen->db_name);
                                }
                            } else {
                                continue;
                            }
                        }
                        if($sayaclar[$i]['adet']>0)
                            $eklenmedurum = true;
                    }
                    try {
                        foreach ($depogelenler as $depogelen) {
                            if ($depogelen->flag == 0) { // silinecek varsa
                                $hatirlatmalar = Hatirlatma::where('depogelen_id', $depogelen->id)->get();
                                $servistakipler = ServisTakip::where('depogelen_id', $depogelen->id)->get();
                                $sayacgelenler = SayacGelen::where('depogelen_id', $depogelen->id)->get();
                                foreach ($hatirlatmalar as $hatirlatma) {
                                    $hatirlatma->delete();
                                }
                                foreach ($servistakipler as $servistakip) {
                                    $servistakip->delete();
                                }
                                foreach ($sayacgelenler as $sayacgelen) {
                                    $sayacgelen->delete();
                                }
                                $depogelen->delete();
                            }
                        }
                    } catch (Exception $e) {
                        Log::error($e);
                        return array('durum' => '0', 'text' => 'Ambar Girişine Ait Silinen Kalemler Güncellenemedi');
                    }
                }
                if($eklenmedurum){
                    try {
                        $faturano = Fatuno::where('SUBE_KODU', $subeyetkili->subekodu)->where('SERI', 'Z')->where('TIP', '9')->first();
                        if (!$faturano) {
                            $faturano = new Fatuno;
                            $faturano->SUBE_KODU = $subeyetkili->subekodu;
                            $faturano->SERI = 'Z';
                            $faturano->TIP = 9;
                            $faturano->NUMARA = 'Z' . '0';
                            $faturano->save();
                        }
                        $belgeno = BackendController::FaturaNo($faturano->NUMARA, 1);
                        Fatuno::where(['SUBE_KODU'=> $subeyetkili->subekodu,'SERI' => 'Z','TIP' => '9'])->update(['NUMARA' => $belgeno]);
                    } catch (Exception $e) {
                        DB::rollBack();
                        Log::error($e);
                        return array('durum' => '0', 'text' => 'Girilen Sayaçlar için Fatura Numarası Alınamadı');
                    }
                    $fatura = null;
                    $faturaek = null;
                    try{
                        $fatura = new Fatuirs;
                        $fatura->SUBE_KODU = $subeyetkili->subekodu;
                        $fatura->FATIRS_NO = BackendController::ReverseTrk($belgeno);  //ornek A00000000633911
                        $fatura->FTIRSIP = '9';    // 9 AMBAR GİRİŞİ
                        $fatura->CARI_KODU = BackendController::ReverseTrk($netsiscari->carikod);
                        $fatura->TARIH = date('Y-m-d', strtotime('first day of january this year'));
                        $fatura->TIPI = 0;
                        $fatura->BRUTTUTAR = 0;
                        $fatura->KDV = 0;
                        $fatura->GENELTOPLAM = 0; //genel toplam
                        $fatura->ACIKLAMA = NULL;
                        $fatura->KOD1 = $servisyetkili->ozelkod;
                        $fatura->ODEMEGUNU = $vadegunu;
                        $fatura->ODEMETARIHI = date('Y-m-d', strtotime($fatura->TARIH . ' + ' . $vadegunu . ' days'));;
                        $fatura->KDV_DAHILMI = 'H';
                        $fatura->FATKALEM_ADEDI = $kalemadedi; //kalem adedi
                        $fatura->SIPARIS_TEST = $fatura->TARIH;
                        $fatura->YEDEK22 = 'A';
                        $fatura->YEDEK = 'X';
                        $fatura->PLA_KODU = $servisyetkili->plasiyerkod;
                        $fatura->DOVIZTIP = 0;
                        $fatura->DOVIZTUT = 0;
                        $fatura->F_YEDEK4 = 1;
                        $fatura->C_YEDEK6 = 'F';
                        $fatura->D_YEDEK10 = $fatura->TARIH;
                        $fatura->PROJE_KODU = $subeyetkili->projekodu;
                        $fatura->KAYITYAPANKUL = $servisyetkili->netsiskullanici;
                        $fatura->KAYITTARIHI = date('Y-m-d H:i:s');
                        $fatura->ISLETME_KODU = 1;
                        $fatura->save();
                    } catch (Exception $e) {
                        Log::error($e);
                        return array('durum' => '0', 'text' => 'Ambar Girişi Kaydedilemedi');
                    }
                        try {
                            $faturaek = new Fatuek;
                            $faturaek->SUBE_KODU = $subeyetkili->subekodu;
                            $faturaek->FKOD = '9';
                            $faturaek->FATIRSNO = BackendController::ReverseTrk($belgeno);
                            $faturaek->CKOD = BackendController::ReverseTrk($netsiscari->carikod);
                            $faturaek->save();
                    } catch (Exception $e) {
                        Log::error($e);
                        $fatura->delete();
                        return array('durum' => '0', 'text' => 'Ambar Girişine Ait Açıklama Kısmı Kaydedilemedi');
                    }
                    try {
                        $i = 1;
                        $inckey = array();
                        foreach ($sayaclar as $sayac) { // kalan adetler için ekleme yapılacak
                            if ($sayac['adet'] > 0) {
                                $faturakalem = new Sthar;
                                $servisstokkod = ServisStokKod::find($sayac['kod']);
                                $faturakalem->STOK_KODU = $servisstokkod->stokkodu;
                                $faturakalem->FISNO = BackendController::ReverseTrk($belgeno);
                                $faturakalem->STHAR_GCMIK = $sayac['adet'];
                                $faturakalem->STHAR_GCKOD = 'G';
                                $faturakalem->STHAR_TARIH = $fatura->TARIH;
                                $faturakalem->STHAR_NF = 0;
                                $faturakalem->STHAR_BF = 0;
                                $faturakalem->STHAR_KDV = 18;
                                $faturakalem->STHAR_DOVTIP = 0;
                                $faturakalem->STHAR_DOVFIAT = 0;
                                $faturakalem->DEPO_KODU = $servisyetkili->depokodu;
                                $faturakalem->STHAR_ACIKLAMA = BackendController::ReverseTrk($netsiscari->carikod);
                                $faturakalem->STHAR_FTIRSIP = '9';
                                $faturakalem->LISTE_FIAT = 0;
                                $faturakalem->STHAR_HTUR = 'A';
                                $faturakalem->STHAR_ODEGUN = $vadegunu;
                                $faturakalem->STHAR_BGTIP = 'I';
                                $faturakalem->STHAR_KOD1 = NULL;
                                $faturakalem->STHAR_KOD2 = 'F';
                                $faturakalem->STHAR_CARIKOD = BackendController::ReverseTrk($netsiscari->carikod);
                                $faturakalem->STHAR_SIP_TURU = 'F';
                                $faturakalem->PLASIYER_KODU = $servisyetkili->plasiyerkod;
                                $faturakalem->SIRA = $i;
                                $faturakalem->STRA_SIPKONT = 0;
                                $faturakalem->IRSALIYE_NO = NULL;
                                $faturakalem->IRSALIYE_TARIH = NULL;
                                $faturakalem->STHAR_TESTAR = NULL;
                                $faturakalem->OLCUBR = 0;
                                $faturakalem->VADE_TARIHI = $fatura->ODEMETARIHI;
                                $faturakalem->SUBE_KODU = $subeyetkili->subekodu;
                                $faturakalem->C_YEDEK6 = 'X';
                                $faturakalem->D_YEDEK10 = $fatura->TARIH;
                                $faturakalem->PROJE_KODU = $subeyetkili->projekodu;
                                $faturakalem->DUZELTMETARIHI = date('Y-m-d H:i:s');
                                $faturakalem->STRA_IRSKONT = 0;
                                $faturakalem->save();
                                $i++;
                                $id = DB::connection('sqlsrv2')->select("SELECT TOP(1)INCKEYNO FROM tblsthar WHERE SUBE_KODU=" . $subeyetkili->subekodu . " AND FISNO='" . BackendController::ReverseTrk($belgeno) . "' and STHAR_FTIRSIP='9' and STHAR_CARIKOD='" . BackendController::ReverseTrk($netsiscari->carikod) . "' ORDER BY INCKEYNO DESC");
                                $inckeyno = ($id[0]->INCKEYNO);
                                if (in_array($inckeyno, $inckey)) { //girilen inckey zaten ekliyse kaydedememiş demektir
                                    $fatura->delete();
                                    $faturaek->delete();
                                    foreach ($inckey as $inckey_) {
                                        Sthar::find($inckey_)->delete();
                                    }
                                    return array('durum' => '0', 'text' => 'Ambar Girişine Ait Kalemler Kısmı Kaydedilemedi');
                                } else { //kaydetme başarılı
                                    array_push($inckey, $inckeyno);
                                    array_push($allkeys, $inckeyno);
                                    array_push($alldbnames, 'MANAS' . date('Y'));
                                }
                            }
                        }
                    }catch (Exception $e) {
                        Log::error($e);
                        $fatura->delete();
                        $faturaek->delete();
                        foreach ($inckey as $inckey_) {
                            Sthar::find($inckey_)->delete();
                        }
                        return array('durum' => '0', 'text' => 'Ambar Girişine Ait Kalemler Kısmı Kaydedilemedi');
                    }
                    return array('durum' => '2', 'fatura' => $fatura, 'faturaek' => $faturaek, 'faturakalemler' => $allkeys,'dbnames'=>$alldbnames);
                }else{
                    return array('durum'=>'3','faturakalemler' => $allkeys,'dbnames'=>$alldbnames);
                }
            }else {
                if ($belgeno != null) {
                    $depogelenler = DepoGelen::where('fisno', $belgeno)->where('carikod',$netsiscari->carikod)->where('subekodu', $subekodu)->where('db_name', $dbname)->get(); // aynı fatura ile giriş yapılanlar
                    foreach ($depogelenler as $depogelen) {
                        $depogelen->flag = 0; // flag 0 kalanlar kontrol sonrası silinecek
                    }
                    $fatura = Fatuirs::where('SUBE_KODU', $subeyetkili->subekodu)->where('FTIRSIP', '9')->where('FATIRS_NO', BackendController::ReverseTrk($belgeno))
                        ->where('CARI_KODU', BackendController::ReverseTrk($netsiscari->carikod))->first();
                    if ($fatura) {
                        try {
                            $eskifatura = clone $fatura;
                            $eskifaturaek = null;
                            $eskikalemler = array();
                            $inckey = array();
                            $fatura->SUBE_KODU = $subeyetkili->subekodu;
                            $fatura->FATIRS_NO = BackendController::ReverseTrk($belgeno);  //ornek A00000000633911
                            $fatura->FTIRSIP = '9';    // 9 AMBAR GİRİŞİ
                            $fatura->CARI_KODU = BackendController::ReverseTrk($netsiscari->carikod);
                            $fatura->TARIH = $gelistarih;
                            $fatura->TIPI = 0;
                            $fatura->BRUTTUTAR = 0;
                            $fatura->KDV = 0;
                            $fatura->GENELTOPLAM = 0; //genel toplam
                            $fatura->ACIKLAMA = NULL;
                            $fatura->KOD1 = $servisyetkili->ozelkod;
                            $fatura->ODEMEGUNU = $vadegunu;
                            $fatura->ODEMETARIHI = $odemetarih;
                            $fatura->KDV_DAHILMI = 'H';
                            $fatura->FATKALEM_ADEDI = $kalemadedi; //kalem adedi
                            $fatura->SIPARIS_TEST = $gelistarih;
                            $fatura->YEDEK22 = 'A';
                            $fatura->YEDEK = 'X';
                            $fatura->PLA_KODU = $servisyetkili->plasiyerkod;
                            $fatura->DOVIZTIP = 0;
                            $fatura->DOVIZTUT = 0;
                            $fatura->F_YEDEK4 = 1;
                            $fatura->C_YEDEK6 = 'F';
                            $fatura->D_YEDEK10 = $gelistarih;
                            $fatura->PROJE_KODU = $subeyetkili->projekodu;
                            $fatura->DUZELTMEYAPANKUL = $servisyetkili->netsiskullanici;
                            $fatura->DUZELTMETARIHI = date('Y-m-d H:i:s');
                            $fatura->ISLETME_KODU = 1;
                            $fatura->save();
                            try {
                                $faturaek = Fatuek::where('SUBE_KODU', $fatura->SUBE_KODU)->where('FKOD', $fatura->FTIRSIP)->where('FATIRSNO', BackendController::ReverseTrk($belgeno))
                                    ->where('CKOD', BackendController::ReverseTrk($netsiscari->carikod))->first();
                                $eskifaturaek = clone $faturaek;
                                $faturaek->SUBE_KODU = $subeyetkili->subekodu;
                                $faturaek->FKOD = '9';
                                $faturaek->FATIRSNO = BackendController::ReverseTrk($belgeno);
                                $faturaek->CKOD = BackendController::ReverseTrk($netsiscari->carikod);
                                $faturaek->save();
                                $i = 1;
                                foreach ($sayaclar as $sayac) {
                                    $flag = 0;
                                    $servisstokkod = ServisStokKod::find($sayac['kod']);
                                    for ($j = 0; $j < $depogelenler->count(); $j++) {
                                        $depogelen = $depogelenler[$j];
                                        if ($depogelen->servisstokkodu == $servisstokkod->stokkodu && $depogelen->flag == 0) {
                                            try {
                                                $flag = 1;
                                                $depogelen->flag = 1;
                                                $faturakalem = Sthar::find($depogelen->inckeyno);
                                                $eskikalem = clone $faturakalem;
                                                array_push($eskikalemler, $eskikalem);
                                                $faturakalem->STOK_KODU = $servisstokkod->stokkodu;
                                                $faturakalem->FISNO = BackendController::ReverseTrk($belgeno);
                                                $faturakalem->STHAR_GCMIK += $sayac['adet'];
                                                $faturakalem->STHAR_GCKOD = 'G';
                                                $faturakalem->STHAR_TARIH = $gelistarih;
                                                $faturakalem->STHAR_NF = 0;
                                                $faturakalem->STHAR_BF = 0;
                                                $faturakalem->STHAR_KDV = 18;
                                                $faturakalem->STHAR_DOVTIP = 0;
                                                $faturakalem->STHAR_DOVFIAT = 0;
                                                $faturakalem->DEPO_KODU = $servisyetkili->depokodu;
                                                $faturakalem->STHAR_ACIKLAMA = BackendController::ReverseTrk($netsiscari->carikod);
                                                $faturakalem->STHAR_FTIRSIP = '9';
                                                $faturakalem->LISTE_FIAT = 0;
                                                $faturakalem->STHAR_HTUR = 'A';
                                                $faturakalem->STHAR_ODEGUN = $vadegunu;
                                                $faturakalem->STHAR_BGTIP = 'I';
                                                $faturakalem->STHAR_KOD1 = NULL;
                                                $faturakalem->STHAR_KOD2 = 'F';
                                                $faturakalem->STHAR_CARIKOD = BackendController::ReverseTrk($netsiscari->carikod);
                                                $faturakalem->STHAR_SIP_TURU = 'F';
                                                $faturakalem->PLASIYER_KODU = $servisyetkili->plasiyerkod;
                                                $faturakalem->SIRA = $i;
                                                $faturakalem->STRA_SIPKONT = 0;
                                                $faturakalem->IRSALIYE_NO = NULL;
                                                $faturakalem->IRSALIYE_TARIH = NULL;
                                                $faturakalem->STHAR_TESTAR = NULL;
                                                $faturakalem->OLCUBR = 0;
                                                $faturakalem->VADE_TARIHI = $odemetarih;
                                                $faturakalem->SUBE_KODU = $subeyetkili->subekodu;
                                                $faturakalem->C_YEDEK6 = 'X';
                                                $faturakalem->D_YEDEK10 = $gelistarih;
                                                $faturakalem->PROJE_KODU = $subeyetkili->projekodu;
                                                $faturakalem->DUZELTMETARIHI = date('Y-m-d H:i:s');
                                                $faturakalem->STRA_IRSKONT = 0;
                                                $faturakalem->save();
                                                array_push($allkeys, $depogelen->inckeyno);
                                                array_push($alldbnames, $depogelen->db_name);
                                                $i++;
                                                break;
                                            } catch (Exception $e) {
                                                Log::error($e);
                                                BackendController::SubeFaturaGeriAl($eskifatura, $eskifaturaek, $eskikalemler, $inckey, $subeyetkili->subekodu);
                                                return array('durum' => '0', 'text' => 'Ambar Girişine Ait Kalemler Kısmı Kaydedilemedi');
                                            }
                                        } else {
                                            continue;
                                        }
                                    }
                                    if ($flag == 0) { //yeni eklemeyse
                                        try {
                                            $faturakalem = new Sthar;
                                            $faturakalem->STOK_KODU = $servisstokkod->stokkodu;
                                            $faturakalem->FISNO = BackendController::ReverseTrk($belgeno);
                                            $faturakalem->STHAR_GCMIK = $sayac['adet'];
                                            $faturakalem->STHAR_GCKOD = 'G';
                                            $faturakalem->STHAR_TARIH = $gelistarih;
                                            $faturakalem->STHAR_NF = 0;
                                            $faturakalem->STHAR_BF = 0;
                                            $faturakalem->STHAR_KDV = 18;
                                            $faturakalem->STHAR_DOVTIP = 0;
                                            $faturakalem->STHAR_DOVFIAT = 0;
                                            $faturakalem->DEPO_KODU = $servisyetkili->depokodu;
                                            $faturakalem->STHAR_ACIKLAMA = BackendController::ReverseTrk($netsiscari->carikod);
                                            $faturakalem->STHAR_FTIRSIP = '9';
                                            $faturakalem->LISTE_FIAT = 0;
                                            $faturakalem->STHAR_HTUR = 'A';
                                            $faturakalem->STHAR_ODEGUN = $vadegunu;
                                            $faturakalem->STHAR_BGTIP = 'I';
                                            $faturakalem->STHAR_KOD1 = NULL;
                                            $faturakalem->STHAR_KOD2 = 'F';
                                            $faturakalem->STHAR_CARIKOD = BackendController::ReverseTrk($netsiscari->carikod);
                                            $faturakalem->STHAR_SIP_TURU = 'F';
                                            $faturakalem->PLASIYER_KODU = $servisyetkili->plasiyerkod;
                                            $faturakalem->SIRA = $i;
                                            $faturakalem->STRA_SIPKONT = 0;
                                            $faturakalem->IRSALIYE_NO = NULL;
                                            $faturakalem->IRSALIYE_TARIH = NULL;
                                            $faturakalem->STHAR_TESTAR = NULL;
                                            $faturakalem->OLCUBR = 0;
                                            $faturakalem->VADE_TARIHI = $odemetarih;
                                            $faturakalem->SUBE_KODU = $subeyetkili->subekodu;
                                            $faturakalem->C_YEDEK6 = 'X';
                                            $faturakalem->D_YEDEK10 = $gelistarih;
                                            $faturakalem->PROJE_KODU = $subeyetkili->projekodu;
                                            $faturakalem->DUZELTMETARIHI = date('Y-m-d H:i:s');
                                            $faturakalem->STRA_IRSKONT = 0;
                                            $faturakalem->save();
                                            $i++;
                                            $id = DB::connection('sqlsrv2')->select("SELECT TOP(1)INCKEYNO FROM tblsthar WHERE SUBE_KODU=" . $subeyetkili->subekodu . " AND FISNO='" . BackendController::ReverseTrk($belgeno) . "' and STHAR_FTIRSIP='9' and STHAR_CARIKOD='" . BackendController::ReverseTrk($netsiscari->carikod) . "' ORDER BY INCKEYNO DESC");
                                            $inckeyno = ($id[0]->INCKEYNO);
                                            if (in_array($inckeyno, $inckey)) { //girilen inckey zaten ekliyse kaydedememiş demektir
                                                BackendController::SubeFaturaGeriAl($eskifatura, $eskifaturaek, $eskikalemler, $inckey, $subeyetkili->subekodu);
                                                return array('durum' => '0', 'text' => 'Ambar Girişine Ait Kalemler Kısmı Kaydedilemedi');
                                            } else { //kaydetme başarılı
                                                array_push($inckey, $inckeyno);
                                                array_push($allkeys, $inckeyno);
                                                array_push($alldbnames, $dbname);
                                            }
                                        } catch (Exception $e) {
                                            Log::error($e);
                                            BackendController::SubeFaturaGeriAl($eskifatura, $eskifaturaek, $eskikalemler, $inckey, $subeyetkili->subekodu);
                                            return array('durum' => '0', 'text' => 'Ambar Girişine Ait Kalemler Kısmı Kaydedilemedi');
                                        }
                                    }
                                }
                                try {
                                    foreach ($depogelenler as $depogelen) {
                                        if ($depogelen->flag == 0) { // silinecek varsa
                                            $faturakalem = Sthar::find($depogelen->inckeyno);
                                            if ($faturakalem)
                                                $faturakalem->delete();
                                            $hatirlatmalar = Hatirlatma::where('depogelen_id', $depogelen->id)->get();
                                            $servistakipler = ServisTakip::where('depogelen_id', $depogelen->id)->get();
                                            $sayacgelenler = SayacGelen::where('depogelen_id', $depogelen->id)->get();
                                            foreach ($hatirlatmalar as $hatirlatma) {
                                                $hatirlatma->delete();
                                            }
                                            foreach ($servistakipler as $servistakip) {
                                                $servistakip->delete();
                                            }
                                            foreach ($sayacgelenler as $sayacgelen) {
                                                $sayacgelen->delete();
                                            }
                                            $depogelen->delete();
                                        }
                                    }
                                } catch (Exception $e) {
                                    Log::error($e);
                                    BackendController::SubeFaturaGeriAl($eskifatura, $eskifaturaek, $eskikalemler, $inckey, $subeyetkili->subekodu);
                                    return array('durum' => '0', 'text' => 'Ambar Girişine Ait Silinen Kalemler Güncellenemedi');
                                }
                                return array('durum' => '1', 'fatura' => $fatura, 'faturaek' => $faturaek, 'faturakalemler' => $allkeys, 'dbnames' => $alldbnames);
                            } catch (Exception $e) {
                                Log::error($e);
                                BackendController::SubeFaturaGeriAl($eskifatura, $eskifaturaek, $eskikalemler, $inckey, $subeyetkili->subekodu);
                                return array('durum' => '0', 'text' => 'Ambar Girişine Ait Açıklama Kısmı Kaydedilemedi');
                            }
                        } catch (Exception $e) {
                            Log::error($e);
                            return array('durum' => '0', 'text' => 'Ambar Girişi Kaydedilemedi');
                        }
                    } else {
                        return array('durum' => '0', 'text' => 'Fatura bulunamadı! Eski yıllara ait olabilir.');
                    }
                } else {
                    try {
                        try {
                            $faturano = Fatuno::where('SUBE_KODU', $subeyetkili->subekodu)->where('SERI', 'Z')->where('TIP', '9')->first();
                            if (!$faturano) {
                                $faturano = new Fatuno;
                                $faturano->SUBE_KODU = $subeyetkili->subekodu;
                                $faturano->SERI = 'Z';
                                $faturano->TIP = 9;
                                $faturano->NUMARA = 'Z' . '0';
                                $faturano->save();
                            }
                            $belgeno = BackendController::FaturaNo($faturano->NUMARA, 1);
                            Fatuno::where(['SUBE_KODU' => $subeyetkili->subekodu, 'SERI' => 'Z', 'TIP' => '9'])->update(['NUMARA' => $belgeno]);
                        } catch (Exception $e) {
                            DB::rollBack();
                            Log::error($e);
                            return array('durum' => '0', 'text' => 'Girilen Sayaçlar için Fatura Numarası Alınamadı');
                        }
                        $fatura = new Fatuirs;
                        $fatura->SUBE_KODU = $subeyetkili->subekodu;
                        $fatura->FATIRS_NO = $belgeno;  //ornek A00000000633911
                        $fatura->FTIRSIP = '9';    // 9 AMBAR GİRİŞİ
                        $fatura->CARI_KODU = BackendController::ReverseTrk($netsiscari->carikod);
                        $fatura->TARIH = $gelistarih;
                        $fatura->TIPI = 0;
                        $fatura->BRUTTUTAR = 0;
                        $fatura->KDV = 0;
                        $fatura->GENELTOPLAM = 0; //genel toplam
                        $fatura->ACIKLAMA = NULL;
                        $fatura->KOD1 = $servisyetkili->ozelkod;
                        $fatura->ODEMEGUNU = $vadegunu;
                        $fatura->ODEMETARIHI = $odemetarih;
                        $fatura->KDV_DAHILMI = 'H';
                        $fatura->FATKALEM_ADEDI = $kalemadedi; //kalem adedi
                        $fatura->SIPARIS_TEST = $gelistarih;
                        $fatura->YEDEK22 = 'A';
                        $fatura->YEDEK = 'X';
                        $fatura->PLA_KODU = $servisyetkili->plasiyerkod;
                        $fatura->DOVIZTIP = 0;
                        $fatura->DOVIZTUT = 0;
                        $fatura->F_YEDEK4 = 1;
                        $fatura->C_YEDEK6 = 'F';
                        $fatura->D_YEDEK10 = $gelistarih;
                        $fatura->PROJE_KODU = $subeyetkili->projekodu;
                        $fatura->KAYITYAPANKUL = $servisyetkili->netsiskullanici;
                        $fatura->KAYITTARIHI = date('Y-m-d H:i:s');
                        $fatura->ISLETME_KODU = 1;
                        $fatura->save();
                    } catch (Exception $e) {
                        Log::error($e);
                        return array('durum' => '0', 'text' => 'Ambar Girişi Kaydedilemedi');
                    }
                    try {
                        $faturaek = new Fatuek;
                        $faturaek->SUBE_KODU = $subeyetkili->subekodu;
                        $faturaek->FKOD = '9';
                        $faturaek->FATIRSNO = $belgeno;
                        $faturaek->CKOD = BackendController::ReverseTrk($netsiscari->carikod);
                        $faturaek->save();
                    } catch (Exception $e) {
                        Log::error($e);
                        $fatura->delete();
                        return array('durum' => '0', 'text' => 'Ambar Girişine Ait Açıklama Kısmı Kaydedilemedi');
                    }
                    try {
                        $i = 1;
                        $inckey = array();
                        foreach ($sayaclar as $sayac) {
                            $faturakalem = new Sthar;
                            $servisstokkod = ServisStokKod::find($sayac['kod']);
                            $faturakalem->STOK_KODU = $servisstokkod->stokkodu;
                            $faturakalem->FISNO = $belgeno;
                            $faturakalem->STHAR_GCMIK = $sayac['adet'];
                            $faturakalem->STHAR_GCKOD = 'G';
                            $faturakalem->STHAR_TARIH = $gelistarih;
                            $faturakalem->STHAR_NF = 0;
                            $faturakalem->STHAR_BF = 0;
                            $faturakalem->STHAR_KDV = 18;
                            $faturakalem->STHAR_DOVTIP = 0;
                            $faturakalem->STHAR_DOVFIAT = 0;
                            $faturakalem->DEPO_KODU = $servisyetkili->depokodu;
                            $faturakalem->STHAR_ACIKLAMA = BackendController::ReverseTrk($netsiscari->carikod);
                            $faturakalem->STHAR_FTIRSIP = '9';
                            $faturakalem->LISTE_FIAT = 0;
                            $faturakalem->STHAR_HTUR = 'A';
                            $faturakalem->STHAR_ODEGUN = $vadegunu;
                            $faturakalem->STHAR_BGTIP = 'I';
                            $faturakalem->STHAR_KOD1 = NULL;
                            $faturakalem->STHAR_KOD2 = 'F';
                            $faturakalem->STHAR_CARIKOD = BackendController::ReverseTrk($netsiscari->carikod);
                            $faturakalem->STHAR_SIP_TURU = 'F';
                            $faturakalem->PLASIYER_KODU = $servisyetkili->plasiyerkod;
                            $faturakalem->SIRA = $i;
                            $faturakalem->STRA_SIPKONT = 0;
                            $faturakalem->IRSALIYE_NO = NULL;
                            $faturakalem->IRSALIYE_TARIH = NULL;
                            $faturakalem->STHAR_TESTAR = NULL;
                            $faturakalem->OLCUBR = 0;
                            $faturakalem->VADE_TARIHI = $odemetarih;
                            $faturakalem->SUBE_KODU = $subeyetkili->subekodu;
                            $faturakalem->C_YEDEK6 = 'X';
                            $faturakalem->D_YEDEK10 = $gelistarih;
                            $faturakalem->PROJE_KODU = $subeyetkili->projekodu;
                            $faturakalem->DUZELTMETARIHI = date('Y-m-d H:i:s');
                            $faturakalem->STRA_IRSKONT = 0;
                            $faturakalem->save();
                            $id = DB::connection('sqlsrv2')->select("SELECT TOP(1)INCKEYNO FROM tblsthar WHERE SUBE_KODU=" . $subeyetkili->subekodu . " AND FISNO='" . $belgeno . "' AND STHAR_FTIRSIP='9' ORDER BY INCKEYNO DESC");
                            $inckeyno = ($id[0]->INCKEYNO);
                            if (in_array($inckeyno, $inckey)) { //girilen inckey zaten ekliyse kaydedememiş demektir
                                $fatura->delete();
                                $faturaek->delete();
                                Sthar::where('STHAR_FTIRSIP', '9')->where('SUBE_KODU', $subeyetkili->subekodu)
                                    ->where('STHAR_CARIKOD', BackendController::ReverseTrk($netsiscari->carikod))
                                    ->where('FISNO', BackendController::ReverseTrk($belgeno))->delete();
                                return array('durum' => '0', 'text' => 'Ambar Girişine Ait Kalemler Kısmı Kaydedilemedi');
                            } else { //kaydetme başarılı
                                array_push($inckey, $inckeyno);
                                array_push($allkeys, $inckeyno);
                                array_push($alldbnames, 'MANAS' . date('Y'));
                            }
                        }
                    } catch (Exception $e) {
                        Log::error($e);
                        $fatura->delete();
                        $faturaek->delete();
                        Sthar::where('STHAR_FTIRSIP', '9')->where('SUBE_KODU', $subeyetkili->subekodu)
                            ->where('STHAR_CARIKOD', BackendController::ReverseTrk($netsiscari->carikod))
                            ->where('FISNO', BackendController::ReverseTrk($belgeno))->delete();
                        return array('durum' => '0', 'text' => 'Ambar Girişine Ait Kalemler Kısmı Kaydedilemedi');
                    }
                    return array('durum' => '1', 'fatura' => $fatura, 'faturaek' => $faturaek, 'faturakalemler' => $inckey, 'dbnames' => $alldbnames);
                }
            }
        } catch (Exception $e) {
            Log::error($e);
            return array('durum'=>'0','text'=>str_replace("'","\'",$e->getMessage()));
        }
    }

    public static function SubeFaturaGrupla($sayacgelenlist){
        $stokkodu=$teslimdurum=array();
        foreach ($sayacgelenlist as $key => $row) {
            $stokkodu[$key] = $row['stokkodu'];
            $teslimdurum[$key] = $row['teslimdurum']; //geri gönderilen sayaçları ayrı gruplamak için eklendi
        }
        array_multisort($teslimdurum,SORT_ASC,$stokkodu, SORT_ASC, $sayacgelenlist);
        $i=0;
        $list=array();
        foreach($sayacgelenlist as $sayacgelen)
        {
            if($i==0)
            {
                array_push($list,$sayacgelen);
                $i++;
            }else{
                if($list[$i-1]['teslimdurum']==$sayacgelen['teslimdurum']){
                    if($list[$i-1]['stokkodu']==$sayacgelen['stokkodu']) {
                        $list[$i - 1]['adet'] += 1;
                    } else {
                        array_push($list, $sayacgelen);
                        $i++;
                    }
                }else{
                    array_push($list, $sayacgelen);
                    $i++;
                }

            }
        }
        return $list;
    }

    public static function NetsisSubeDepolararasi($depolararasiid){
        try {
            $sabit = SistemSabitleri::where('adi', 'NetsisFatura')->first();
            if ($sabit->deger == 0) //fatura kesilmeyecek
            {
                return 0;
            } else {
                $depolararasi = Depolararasi::find($depolararasiid);
                $netsiscari = NetsisCari::find($depolararasi->netsiscari_id);
                $vadegunu = is_null($netsiscari->vadegunu) ? 0 : $netsiscari->vadegunu;
                $odemetarih = date('Y-m-d', strtotime(date('Y-m-d') . ' + ' . $vadegunu . ' days'));
                $secilenler = explode(',', $depolararasi->secilenler);
                $sayacgelenler = SayacGelen::whereIn('id', $secilenler)->get();
                foreach ($sayacgelenler as $sayacgelen) {
                    $sayacgelen->adet=1;
                }
                $sayacgelenlist = $sayacgelenler->toArray();
                $subeyetkili = SubeYetkili::where('kullanici_id', Auth::user()->id)->where('aktif',1)->first();
                try {
                    if (is_null($depolararasi->faturano)) {
                        $faturano = Fatuno::where('SUBE_KODU', $subeyetkili->subekodu)->where('SERI', 'A')->where('TIP', '8')->where('ALTTIP', null)->first(); //irsaliyesiz ise A tipinde numara alacak
                        if (!$faturano) {
                            $faturano = new Fatuno;
                            $faturano->SUBE_KODU = $subeyetkili->subekodu;
                            $faturano->SERI = 'A';
                            $faturano->TIP = 8;
                            $faturano->ALTTIP = null;
                            $faturano->NUMARA = 'A' . '0';
                            $faturano->save();
                        }
                        $fatirsno = BackendController::FaturaNo($faturano->NUMARA, 1);
                        Fatuno::where(['SUBE_KODU' => $subeyetkili->subekodu, 'SERI' => 'A', 'TIP' => '8', 'ALTTIP' => NULL])->update(['NUMARA' => $fatirsno]);
                    } else {
                        $fatirsno = BackendController::FaturaNo($depolararasi->faturano, 0);
                    }
                } catch (Exception $e) {
                    Log::error($e);
                    return array('durum' => 6);
                }
                $fatura = Fatuirs::where('SUBE_KODU', $subeyetkili->subekodu)->where('FTIRSIP', 8)->where('FATIRS_NO', $fatirsno)->first();
                if ($fatura) {
                    if (is_null($depolararasi->faturano)) {
                        $sonfatura = Fatuirs::where('SUBE_KODU', $subeyetkili->subekodu)->where('FTIRSIP', 8)->where('FATIRS_NO', 'LIKE', 'A%')->orderBy('FATIRS_NO', 'desc')->first();
                        if ($sonfatura) {
                            $fatirsno = BackendController::FaturaNo($sonfatura->FATIRS_NO, 1);
                            Fatuno::where(['SUBE_KODU'=>$subeyetkili->subekodu,'SERI' => 'A', 'TIP' => '8', 'ALTTIP' => NULL])->update(['NUMARA' => $fatirsno]);
                        } else {
                            return array('durum' => 5);
                        }
                    } else {
                        return array('durum' => 5);
                    }
                }
                try {
                    $fatura = new Fatuirs;
                    $faturaek = new Fatuek;
                    $fatura->SUBE_KODU = $subeyetkili->subekodu;
                    $fatura->FATIRS_NO = $fatirsno;  //ornek A00000000633911
                    $fatura->FTIRSIP = '8';
                    $fatura->CARI_KODU = '000000000000000'; //$depolararasi->carikod;
                    $fatura->TARIH = date('Y-m-d');
                    $fatura->TIPI = 0;
                    $fatura->ODEMEGUNU = $vadegunu;
                    $fatura->ODEMETARIHI = $odemetarih;
                    $fatura->KDV_DAHILMI = 'H';
                    $fatura->SIPARIS_TEST = date('Y-m-d');
                    $fatura->CARI_KOD2 = BackendController::ReverseTrk($depolararasi->carikod);
                    $fatura->YEDEK = 'D';
                    $fatura->PLA_KODU = $depolararasi->plasiyerkod;
                    $fatura->C_YEDEK6 = 'B';
                    $fatura->D_YEDEK10 = date('Y-m-d');
                    $fatura->PROJE_KODU = '1';
                    $fatura->KAYITYAPANKUL = $depolararasi->netsiskullanici;
                    $fatura->KAYITTARIHI = date('Y-m-d H:i:s');
                    $fatura->ISLETME_KODU = 1;

                    try {
                        $faturaek->SUBE_KODU = $subeyetkili->subekodu;
                        $faturaek->FKOD = '8';
                        $faturaek->FATIRSNO = $fatirsno;
                        $faturaek->CKOD = '000000000000000';
                        if($depolararasi->tipi==1)
                            $faturaek->ACIK7 = BackendController::ReverseTrk('HURDA SAYAÇLARDIR FATURA EDİLMEYECEKTİR.');
                        else
                            $faturaek->ACIK7 = BackendController::ReverseTrk('DEPOLAR ARASI SEVKTİR FATURA EDİLMEYECEKTİR.');
                        $faturaek->ACIK8 = BackendController::ReverseTrk('SAYAÇ LİSTESİ EKTEDİR.');
                        $faturaek->save();

                        $kalemler = BackendController::SubeFaturaGrupla($sayacgelenlist);
                        $i = 1;
                        $inckey = array();
                        $inckeygelen = array();
                        $teslimdurum=array();
                        foreach ($kalemler as $kalem) { // DEPO ÇIKIŞ
                            try {
                                $faturakalem = new Sthar;
                                $faturakalem->STOK_KODU = $kalem['stokkodu'];
                                $faturakalem->FISNO = $fatirsno;
                                $faturakalem->STHAR_GCMIK = $kalem['adet'];
                                $faturakalem->STHAR_GCKOD = 'C';
                                $faturakalem->STHAR_TARIH = date('Y-m-d');
                                $faturakalem->STHAR_NF = 0;
                                $faturakalem->STHAR_BF = 0;
                                $faturakalem->STHAR_DOVTIP = 0;
                                $faturakalem->STHAR_DOVFIAT = 0;
                                $faturakalem->STHAR_KDV = 0;
                                $faturakalem->DEPO_KODU = $depolararasi->depokodu;
                                $faturakalem->STHAR_ACIKLAMA = $depolararasi->depokodu . '-' . $depolararasi->aktarilandepo;
                                $faturakalem->STHAR_FTIRSIP = '8';
                                $faturakalem->LISTE_FIAT = 1;
                                $faturakalem->STHAR_HTUR = 'B';
                                $faturakalem->STHAR_ODEGUN = $vadegunu;
                                $faturakalem->STHAR_BGTIP = 'I';
                                $faturakalem->STHAR_CARIKOD = BackendController::ReverseTrk($depolararasi->carikod);
                                $faturakalem->PLASIYER_KODU = $depolararasi->plasiyerkod;
                                $faturakalem->SIRA = $i;
                                $faturakalem->STRA_SIPKONT = 0;
                                $faturakalem->OLCUBR = 1;
                                $faturakalem->VADE_TARIHI = $odemetarih;
                                $faturakalem->SUBE_KODU = $subeyetkili->subekodu;
                                $faturakalem->C_YEDEK6 = 'D';
                                $faturakalem->I_YEDEK8 = $depolararasi->aktarilandepo;
                                $faturakalem->D_YEDEK10 = date('Y-m-d');
                                $faturakalem->PROJE_KODU = '1';
                                $faturakalem->DUZELTMETARIHI = date('Y-m-d');
                                $faturakalem->save();

                                $id = DB::connection('sqlsrv2')->select("SELECT TOP(1)INCKEYNO FROM tblsthar WHERE SUBE_KODU='" . $subeyetkili->subekodu . "' AND FISNO='" . $fatirsno . "' AND STHAR_FTIRSIP='8' ORDER BY INCKEYNO DESC");
                                $inckeyno = ($id[0]->INCKEYNO);
                                if (in_array($inckeyno, $inckey)) { //girilen inckey zaten ekliyse kaydedememiş demektir
                                    return array('durum' => 4);
                                } else { //kaydetme başarılı
                                    array_push($inckey, $inckeyno);
                                }
                            } catch (Exception $e) {
                                Log::error($e);
                                return array('durum' => 4);
                            }
                            try {// AKTARILAN DEPO GİRİŞ
                                $faturakalem = new Sthar;
                                $faturakalem->STOK_KODU = $kalem['stokkodu'];
                                $faturakalem->FISNO = $fatirsno;
                                $faturakalem->STHAR_GCMIK = $kalem['adet'];
                                $faturakalem->STHAR_GCKOD = 'G';
                                $faturakalem->STHAR_TARIH = date('Y-m-d');
                                $faturakalem->STHAR_NF = 0;
                                $faturakalem->STHAR_BF = 0;
                                $faturakalem->STHAR_DOVTIP = 0;
                                $faturakalem->STHAR_DOVFIAT = 0;
                                $faturakalem->STHAR_KDV = 0;
                                $faturakalem->DEPO_KODU = $depolararasi->aktarilandepo;
                                $faturakalem->STHAR_ACIKLAMA = $depolararasi->depokodu . '-' . $depolararasi->aktarilandepo;
                                $faturakalem->STHAR_FTIRSIP = '9';
                                $faturakalem->LISTE_FIAT = 1;
                                $faturakalem->STHAR_HTUR = 'B';
                                $faturakalem->STHAR_ODEGUN = $vadegunu;
                                $faturakalem->STHAR_BGTIP = 'I';
                                $faturakalem->STHAR_CARIKOD = BackendController::ReverseTrk($depolararasi->carikod);
                                $faturakalem->PLASIYER_KODU = $depolararasi->plasiyerkod;
                                $faturakalem->SIRA = $i;
                                $faturakalem->STRA_SIPKONT = 0;
                                $faturakalem->OLCUBR = 1;
                                $faturakalem->VADE_TARIHI = $odemetarih;
                                $faturakalem->SUBE_KODU = $subeyetkili->subekodu;
                                $faturakalem->C_YEDEK6 = 'D';
                                $faturakalem->I_YEDEK8 = $depolararasi->depokodu;
                                $faturakalem->D_YEDEK10 = date('Y-m-d');
                                $faturakalem->PROJE_KODU = '1';
                                $faturakalem->DUZELTMETARIHI = date('Y-m-d');
                                $faturakalem->save();
                                $id = DB::connection('sqlsrv2')->select("SELECT TOP(1)INCKEYNO FROM tblsthar WHERE SUBE_KODU='" . $subeyetkili->subekodu . "' AND FISNO='" . $fatirsno . "' AND STHAR_FTIRSIP='9' ORDER BY INCKEYNO DESC");
                                $inckeyno = ($id[0]->INCKEYNO);
                                if (in_array($inckeyno, $inckeygelen)) { //girilen inckey zaten ekliyse kaydedememiş demektir
                                    return array('durum' => 4);
                                } else { //kaydetme başarılı
                                    array_push($inckeygelen, $inckeyno);
                                }
                            } catch (Exception $e) {
                                Log::error($e);
                                return array('durum' => 4);
                            }
                            $i++;
                            array_push($teslimdurum,$kalem['teslimdurum']);
                        }
                    } catch (Exception $e) {
                        Log::error($e);
                        return array('durum' => 2);
                    }
                    $fatura->BRUTTUTAR = 0; //toplam
                    $fatura->KDV = 0; //kdv si
                    $fatura->FATKALEM_ADEDI = ($i - 1); //kalem adedi
                    $fatura->GENELTOPLAM = 0; //genel toplam
                    $fatura->save();
                    $depolararasi->db_name = Config::get('database.connections.sqlsrv2.database');
                    $depolararasi->faturano = $fatirsno;
                    $depolararasi->save();
                    return array('durum' => 1, 'kalemler' => $inckeygelen,'teslimdurum'=>$teslimdurum);
                } catch (Exception $e) {
                    Log::error($e);
                    return array('durum'=>3);
                }
            }
        } catch (Exception $e) {
            Log::error($e);
            return array('durum'=>str_replace("'","\'",$e->getMessage()));
        }
    }

    public static function SubeSayacGrupla($sayacgelenlist){
        foreach ($sayacgelenlist as $key => $row) {
            $stokkod[$key] = $row['stokkodu'];
        }

        array_multisort($stokkod, SORT_ASC, $sayacgelenlist);
        $i=0;
        $list=array();
        $serinolar="";
        foreach($sayacgelenlist as $sayacgelen)
        {
            if($i==0)
            {
                array_push($list,$sayacgelen);
                $serinolar.=($serinolar=="" ? "" : ", ").$sayacgelen['serino'];
                $i++;
            }else{
                if($list[$i-1]['stokkodu']==$sayacgelen['stokkodu']) {
                    $list[$i - 1]['adet'] += 1;
                } else {
                    array_push($list, $sayacgelen);
                    $i++;
                }
                $serinolar.=($serinolar=="" ? "" : ", ").$sayacgelen['serino'];
            }
        }
        return array("list"=>$list,"serino"=>$serinolar);
    }

    public static function FaturaNo($faturano,$inc){
        if ($faturano == "") {
            return "";
        }
        $digits = str_split($faturano);
        $index = -1;
        for ($i = 0; $i < count($digits); $i++) {
            if (!is_numeric($digits[$i])) {
                $index = $i;
            }else if(($digits[$i]>0)){
                $index = $i;
            }else if($digits[$i]==0)
                break;
        }
        $subkey = substr($faturano,0,$index+1);
        $faturaek = substr($faturano,strlen($subkey),15-strlen($subkey));
        if(isset($faturaek) && strlen($faturaek)>0){
            $digitlength=15-strlen($subkey);
            $pattern="%0".$digitlength."d";
            return $subkey.preg_replace_callback( '/(\\d+)/', function($match) use($pattern,$inc) { return (sprintf($pattern,($match[0]+$inc))); }, $faturaek );
        }
        return "";
    }

    public static function FaturaOnEk($sayacsatis){
        if($sayacsatis->efatura){ //efatura müşterisi ise
            $param = EfaturaParam::where('SIRKET',$sayacsatis->db_name)->where('SUBE_KODU',$sayacsatis->subekodu)->first();
            if($param){
                return $param->TERCIH_EDILEN_SERI_NO;
            }else{
                return 'ME0';
            }
        }else{
            $param = EarsivParam::where('SIRKET',$sayacsatis->db_name)->where('SUBE_KODU',$sayacsatis->subekodu)->first();
            if($param){
                return $param->TERCIH_EDILEN_SERI_NO;
            }else{
                return 'EA'.$sayacsatis->subekodu;
            }
        }
    }

    public static function GibFaturaNo($faturano,$subkey){
        if ($faturano == "") {
            return "";
        }
        $year = date('Y');
        return substr_replace($faturano,$year."0",strlen($subkey),strlen($year."0"));
    }

    public static function FisNo($faturano,$subkey,$inc){
        if($faturano==""){
            return "";
        }
        $faturaek = substr($faturano,strlen($subkey),15-strlen($subkey));
        if(isset($faturaek) && strlen($faturaek)>0){
            $digitlength=15-strlen($subkey);
            $pattern="%0".$digitlength."d";
            return $subkey.preg_replace_callback( '/(\\d+)/', function($match) use($pattern,$inc) { return (sprintf($pattern,($match[0]+$inc))); }, $faturaek );
        }
        return "";
    }

    public static function StringZeroAdd($string,$length){
        if($string==""){
            return "";
        }
        preg_match('/^\D*(?=\d)/', $string, $firstdigit);
        if(isset($firstdigit[0])){
            if(strlen($firstdigit[0])>($length-1)){
                return "";
            }
            $digitlength=$length-strlen($firstdigit[0]);
            $pattern="%0".$digitlength."d";
            return preg_replace_callback( '/(\\d+)/', function($match) use($pattern) { return (sprintf($pattern,($match[0]))); }, $string );
        }
        return "";
    }

    public static function SatisFaturasi($id){ // Netsis Tarafında Satış Faturasını Karşılar
        try {
            $sabit = SistemSabitleri::where('adi', 'NetsisFatura')->first();
            if ($sabit->deger == 0) //fatura kesilmeyecek
            {
                return 0;
            } else {
                $sayacsatis = SubeSayacSatis::find($id);
                $sube = Sube::where('subekodu',$sayacsatis->subekodu)->where('aktif',1)->first();
                $subeyetkili = SubeYetkili::where('kullanici_id', Auth::user()->id)->where('aktif', 1)->first();
                $netsiscari = NetsisCari::find($sayacsatis->netsiscari_id);
                $vadegunu = is_null($netsiscari->vadegunu) ? 0 : $netsiscari->vadegunu;
                $odemetarih = date('Y-m-d', strtotime($sayacsatis->faturatarihi . ' + ' . $vadegunu . ' days'));
                $abone = Abone::find($sayacsatis->abone_id);
                $aboneil = Iller::find($abone->iller_id);
                $aboneilce = Ilceler::find($abone->ilceler_id);
                $fatirsno = $sayacsatis->faturano;
                $parabirimi = ParaBirimi::find($sayacsatis->parabirimi_id);
                $secilenler = explode(',', $sayacsatis->secilenler);
                $sayaclar = explode(';', $sayacsatis->sayaclar);
                $adetler = explode(',', $sayacsatis->adet);
                $birimfiyatlar = explode(',', $sayacsatis->birimfiyat);
                $ucretsizler = explode(',', $sayacsatis->ucretsiz);
                $urunler = array();
                $status = 0;
                for ($i = 0; $i < count($secilenler); $i++) {
                    $urun = SubeUrun::find($secilenler[$i]);
                    $urun->adet = $adetler[$i];
                    $urun->fiyat = $birimfiyatlar[$i];
                    $urun->ucretsiz = $ucretsizler[$i];
                    $urun->stokkodu = NetsisStokKod::find($urun->netsisstokkod_id);
                    if ($urun->baglanti) {
                        $sayaclist = explode(',', $sayaclar[$i]);
                        $urun->sayaclar = AboneSayac::where('sayacadi_id', $urun->sayacadi_id)->where('sayaccap_id', $urun->sayaccap_id)
                            ->where('uretimyer_id', $sayacsatis->uretimyer_id)->whereIn('id', $sayaclist)->get();
                    } else {
                        $urun->sayaclar = array();
                    }
                    array_push($urunler, $urun);
                }
                try {
                    $fatura = Fatuirs::where('SUBE_KODU', $sayacsatis->subekodu)->where('FTIRSIP', 1)->where('FATIRS_NO', $fatirsno)->first();
                    if ($fatura) {
                        return 5;
                    }
                    if($sayacsatis->odemesekli=="NAKİT+KREDİ KARTI"){
                        $status = 1;
                        $kasakod = KasaKod::where('subekodu', $subeyetkili->subekodu)->where('kasakod', $sayacsatis->kasakodu)->first();
                        $sayacsatis->odemesekli = 'NAKİT:'.floatval($sayacsatis->odeme).' '.$parabirimi->yazi.' + K.KARTI('.$kasakod->kisaadi.'):'.floatval($sayacsatis->odeme2).' '.$parabirimi->yazi;
                    }else if($sayacsatis->odemesekli=="2 KREDİ KARTI"){
                        $status = 1;
                        $kasakod = KasaKod::where('subekodu', $subeyetkili->subekodu)->where('kasakod', $sayacsatis->kasakodu)->first();
                        $kasakod2 = KasaKod::where('subekodu', $subeyetkili->subekodu)->where('kasakod', $sayacsatis->kasakodu2)->first();
                        $sayacsatis->odemesekli = 'K.KARTI('.$kasakod->kisaadi.'):'.floatval($sayacsatis->odeme).' '.$parabirimi->yazi.' + K.KARTI('.$kasakod2->kisaadi.'):'.floatval($sayacsatis->odeme2).' '.$parabirimi->yazi;
                    }else if($sayacsatis->odemesekli=="KREDİ KARTI"){
                        $kasakod = KasaKod::where('subekodu', $subeyetkili->subekodu)->where('kasakod', $sayacsatis->kasakodu)->first();
                        $sayacsatis->odemesekli = $sayacsatis->odemesekli.'('.$kasakod->kisaadi.')';
                    }else if($sayacsatis->odemesekli=="BANKA HAVALESİ"){
                        $status  = 1;
                    }else if($sayacsatis->odemesekli=="BELLİ DEĞİL"){
                        $status  = 1;
                    }
                    $sayacsatis->save();
                    $fatura = new Fatuirs;
                    $faturaek = new Fatuek;
                    $fatura->SUBE_KODU = $sayacsatis->subekodu;
                    $fatura->FATIRS_NO = $fatirsno;
                    $fatura->FTIRSIP = '1';
                    $fatura->CARI_KODU = BackendController::ReverseTrk($sayacsatis->carikod);
                    $fatura->TARIH = $sayacsatis->faturatarihi;
                    $fatura->TIPI = $status ? 2 : 1; //1 Kapalı Fatura 2 Açık Fatura
                    $fatura->BRUTTUTAR = $sayacsatis->tutar;
                    $fatura->KDV = $sayacsatis->kdv;
                    $fatura->ACIKLAMA = BackendController::ReverseTrk(mb_substr(trim($abone->adisoyadi),0,
                        (strlen(utf8_decode(trim($abone->adisoyadi)))>20 ? 20 : strlen(utf8_decode(trim($abone->adisoyadi)))),'utf-8'));
                    $fatura->KOD1 = '0';
                    $fatura->ODEMEGUNU = $vadegunu;
                    $fatura->ODEMETARIHI = $odemetarih;
                    $fatura->KDV_DAHILMI = 'E';
                    $fatura->FATKALEM_ADEDI = count($urunler);
                    $fatura->SIPARIS_TEST = $sayacsatis->faturatarihi;
                    $fatura->GENELTOPLAM = $sayacsatis->toplamtutar;
                    $fatura->PLA_KODU = $sayacsatis->plasiyerkod;
                    $fatura->KS_KODU = $sayacsatis->kasakodu;
                    $fatura->C_YEDEK6 = 'X';
                    $fatura->D_YEDEK10 = $sayacsatis->faturatarihi;
                    $fatura->PROJE_KODU = $sayacsatis->projekodu;
                    if($status) $fatura->FIYATTARIHI = $sayacsatis->faturatarihi;
                    $fatura->KAYITYAPANKUL = $sayacsatis->netsiskullanici;
                    $fatura->KAYITTARIHI = date('Y-m-d H:i:s');
                    $fatura->DUZELTMEYAPANKUL = $sayacsatis->netsiskullanici;
                    $fatura->DUZELTMETARIHI = date('Y-m-d H:i:s');
                    $fatura->GELSUBE_KODU = 0;
                    $fatura->GITSUBE_KODU = 0;
                    $fatura->ISLETME_KODU = 1;
                    $fatura->KOSVADEGUNU = 0;
                    $fatura->GIB_FATIRS_NO = BackendController::GibFaturaNo($fatirsno,BackendController::FaturaOnEk($sayacsatis));
                    $fatura->SIST_EARSIVMI = NULL;
                    $fatura->EBELGE = 0;
                    $fatura->save();
                } catch (Exception $e) {
                    Log::error($e);
                    return 2;
                }
                try {
                    $faturaek->SUBE_KODU = $sayacsatis->subekodu;
                    $faturaek->FKOD = '1';
                    $faturaek->FATIRSNO = $fatirsno;
                    $faturaek->CKOD = BackendController::ReverseTrk($sayacsatis->carikod);
                    if($sayacsatis->netsiscari_id==$sube->netsiscari_id){
                        if($abone->vergidairesi!=""){
                            $faturaek->ACIK1 = $abone->tckimlikno;
                            $faturaek->ACIK6 = BackendController::reverseTrk($abone->vergidairesi);
                        }else{
                            $faturaek->ACIK1 = $abone->tckimlikno;
                        }
                        $faturaek->ACIK3 = BackendController::reverseTrk($abone->adisoyadi);
                        $faturaek->ACIK4 = BackendController::reverseTrk($abone->adisoyadi);
                        $faturaek->ACIK5 = BackendController::reverseTrk($abone->adisoyadi);
                    }else{
                        $faturaek->ACIK1 = $netsiscari->vergino;
                        $faturaek->ACIK6 = BackendController::reverseTrk($netsiscari->vergidairesi);
                        $faturaek->ACIK3 = BackendController::reverseTrk($netsiscari->cariadi);
                    }
                    $faturaek->ACIK7 = BackendController::reverseTrk($abone->faturaadresi);
                    $faturaek->ACIK8 = BackendController::reverseTrk($aboneil->adi);
                    $faturaek->ACIK9 = BackendController::reverseTrk($aboneilce->adi);
                    $faturaek->ACIK10 = $abone->email=="" ? null : $abone->email;
                    $faturaek->ACIK11 = $abone->telefon;
                    $faturaek->ACIK14 = BackendController::reverseTrk($sayacsatis->aciklama);
                    $faturaek->ACIK15 = BackendController::reverseTrk($sayacsatis->odemesekli=="BELLİ DEĞİL" ? "" : $sayacsatis->odemesekli);
                    $faturaek->ACIK16 = BackendController::reverseTrk($abone->aciklama);
                    $faturaek->save();
                } catch (Exception $e) {
                    Log::error($e);
                    return 3;
                }
                try {
                    $inckey = array();
                    for ($i = 0; $i < count($urunler); $i++) {
                        $faturakalem = new Sthar;
                        $faturakalem->STOK_KODU = $urunler[$i]->stokkodu->kodu;
                        $faturakalem->FISNO = $fatirsno;
                        $faturakalem->STHAR_GCMIK = $urunler[$i]->adet;
                        $faturakalem->STHAR_GCKOD = 'C';
                        $faturakalem->STHAR_TARIH = $sayacsatis->faturatarihi;
                        $kalemtutar = $urunler[$i]->fiyat;
                        $kalembrut = BackendController::TruncateNumber((($kalemtutar*100)/118),8);
                        $faturakalem->STHAR_NF = $kalembrut;
                        $faturakalem->STHAR_BF = $kalemtutar;
                        $faturakalem->STHAR_DOVTIP = 0;
                        $faturakalem->STHAR_DOVFIAT = 0;
                        $faturakalem->STHAR_KDV = 18;
                        $faturakalem->DEPO_KODU = $urunler[$i]->depokodu;
                        $faturakalem->STHAR_ACIKLAMA = BackendController::ReverseTrk($sayacsatis->carikod);
                        $faturakalem->STHAR_FTIRSIP = '1';
                        $faturakalem->LISTE_FIAT = 1;
                        $faturakalem->STHAR_HTUR = 'I';
                        $faturakalem->STHAR_ODEGUN = $vadegunu;
                        $faturakalem->STHAR_BGTIP = 'F';
                        $faturakalem->STHAR_KOD1 = '0';
                        $faturakalem->STHAR_CARIKOD = BackendController::ReverseTrk($sayacsatis->carikod);
                        $faturakalem->PLASIYER_KODU = $sayacsatis->plasiyerkod;
                        $faturakalem->EKALAN_NEDEN = 1;
                        $faturakalem->EKALAN = BackendController::ReverseTrk($urunler[$i]->urunadi);
                        $faturakalem->SIRA = $i + 1;
                        $faturakalem->STRA_SIPKONT = 0;
                        $faturakalem->OLCUBR = 1;
                        $faturakalem->VADE_TARIHI = $odemetarih;
                        $faturakalem->SUBE_KODU = $sayacsatis->subekodu;
                        $faturakalem->D_YEDEK10 = $sayacsatis->faturatarihi;
                        $faturakalem->PROJE_KODU = $sayacsatis->projekodu;
                        $faturakalem->DUZELTMETARIHI = date('Y-m-d H:i:s');
                        if($status) $faturakalem->FIYATTARIHI = date('Y-m-d');
                        $faturakalem->save();
                        $faturainckey = DB::connection('sqlsrv2')->select("SELECT TOP(1)INCKEYNO FROM tblsthar WHERE SUBE_KODU='".$sayacsatis->subekodu."' and FISNO='" . $fatirsno . "' and STHAR_FTIRSIP='1' ORDER BY INCKEYNO DESC");
                        $inckeyno = ($faturainckey[0]->INCKEYNO);
                        if (in_array($inckeyno, $inckey)) { //girilen inckey zaten ekliyse kaydedememiş demektir
                            return 7;
                        } else { //kaydetme başarılı
                            array_push($inckey, $inckeyno);
                        }
                    }
                } catch (Exception $e) {
                    Log::error($e);
                    return 4;
                }

                $durum = BackendController::SeriKaydet($sayacsatis, $inckey);
                if ($durum != 1) {
                    return 10;
                }
                if(!$status){ //AÇIK FATURA KASA KAYIDI YAPILMIYOR
                    $durum = BackendController::KasaKaydet($sayacsatis, 1);
                    if ($durum != 1) {
                        return 6;
                    }
                }

                $durum = BackendController::MuhasebeFisKaydet($sayacsatis, $status);
                if ($durum != 1) {
                    return 11;
                }

                $durum = BackendController::CariHareketKaydet($sayacsatis, $status);
                if ($durum != 1) {
                    return 12;
                }

                $durum = BackendController::ServisBilgisiEkle($sayacsatis, 0); // Servis Bilgisi Eklenecek 0 Satış 1 Tamir sonrası servis 2 arıza kontrolü
                if ($durum == 0) {
                    if(!$status) //AÇIK FATURA KASA KAYIDI YAPILMIYOR
                        Kasa::where('FISNO', $fatirsno)->update(array('GECERLI'=>'H'));
                    return 8;
                } else if ($durum == 2) {
                    if(!$status) //AÇIK FATURA KASA KAYIDI YAPILMIYOR
                    Kasa::where('FISNO', $fatirsno)->update(array('GECERLI'=>'H'));
                    return 9;
                }else{
                    return 1;
                }
            }
        } catch (Exception $e) {
            Log::error($e);
            return str_replace("'","\'",$e->getMessage());
        }
    }

    public static function YaziTutar($tutar,$parabirimi_id){
        $yazi="";$kdvyazi="";
        $parabirimi=ParaBirimi::find($parabirimi_id);
        $tutar = explode('.',$tutar);
        if(count($tutar)!=2) return "";
        $money_left = $tutar['0'];
        $money_right = $tutar['1'];
        $birler = array( "", "BİR", "İKİ", "ÜÇ", "DÖRT", "BEŞ", "ALTI", "YEDİ", "SEKİZ", "DOKUZ" );
        $onlar  = array( "", "ON", "YİRMİ", "OTUZ", "KIRK", "ELLİ", "ALTMIŞ", "YETMİŞ", "SEKSEN", "DOKSAN" );
        $binler = array( "TRİLYON","MİLYAR","MİLYON", "BİN", "" );
        $grupSayisi = 5;
        $money_left=str_pad($money_left,$grupSayisi*3,"0",STR_PAD_LEFT);
        for ($i=0;$i<$grupSayisi*3;$i+=3){ //sayı 3'erli gruplar halinde ele alınıyor.
            $grupDegeri="";
            if (substr($money_left,$i, 1) != "0")
                $grupDegeri .= $birler[intval(substr($money_left,$i, 1))] . " YÜZ"; //yüzler

            if ($grupDegeri == "BİR YÜZ") //biryüz düzeltiliyor.
                $grupDegeri = "YÜZ";

            $grupDegeri .= ($grupDegeri == "" ? "" : (substr($money_left,$i + 1, 1)=="0" ? "" : " ")).$onlar[intval(substr($money_left,$i + 1, 1))]; //onlar

            $grupDegeri .= ($grupDegeri == "" ? "" : (substr($money_left,$i + 2, 1)=="0" ? "" : " ")).$birler[intval(substr($money_left,$i + 2, 1))]; //birler

            if ($grupDegeri != "") //binler
                if($binler[$i/3]!="")
                    $grupDegeri .= ($grupDegeri == "" ? "" : " ").$binler[$i / 3];

            if ($grupDegeri == "BİR BİN") //birbin düzeltiliyor.
                $grupDegeri = "BİN";

            $yazi .= ($yazi == "" ? "" : " ").$grupDegeri;
        }

        if ($yazi != "")
            $yazi .= " ".$parabirimi->adi;

        if (substr($money_right,0, 1) != "0") //kuruş onlar
            $kdvyazi .= ($kdvyazi == "" ? "" : (substr($money_right,0, 1)=="0" ? "" : " ")).$onlar[intval(substr($money_right,0, 1))];

        if (substr($money_right,1, 1) != "0") //kuruş birler
            $kdvyazi .= ($kdvyazi == "" ? "" : (substr($money_right,1, 1)=="0" ? "" : " ")).$birler[intval(substr($money_right,1, 1))];

        if ($kdvyazi !="")
            $kdvyazi .= " ".$parabirimi->madenibirim;
        else
            $kdvyazi .= "SIFIR ".$parabirimi->madenibirim;
        if($yazi!="")
            $yazi .= " ".$kdvyazi;
        else
            $yazi .= "SIFIR ".$parabirimi->adi." ".$kdvyazi;
        return $yazi;
    }

    public static function HatirlatmaEkle($hatirlatmatip,$netsiscari,$servis,$adet,$depogelenid=false,$stokkod=false)
    { //adet kadarlık hatırlatma ekler
        if($stokkod)
            if($depogelenid)
                $hatirlatma_durum = Hatirlatma::select('hatirlatma.id')
                    ->where('hatirlatma.hatirlatmatip_id', $hatirlatmatip)->where('hatirlatma.tip', 2)->where('hatirlatma.durum', 0)
                    ->where('hatirlatma.netsiscari_id', $netsiscari)->where('hatirlatma.servis_id', $servis)->where('hatirlatma.depogelen_id',$depogelenid)
                    ->where('hatirlatma.servisstokkodu',$stokkod)->orderBy('id','desc')->first();
            else
                $hatirlatma_durum = Hatirlatma::select('hatirlatma.id')
                    ->where('hatirlatma.hatirlatmatip_id', $hatirlatmatip)->where('hatirlatma.tip', 2)->where('hatirlatma.durum', 0)
                    ->where('hatirlatma.netsiscari_id', $netsiscari)->where('hatirlatma.servis_id', $servis)
                    ->where('hatirlatma.servisstokkodu',$stokkod)->orderBy('id','desc')->first();
        else
            if($depogelenid)
                $hatirlatma_durum = Hatirlatma::select('hatirlatma.id')
                    ->where('hatirlatma.hatirlatmatip_id', $hatirlatmatip)->where('hatirlatma.tip', 2)->where('hatirlatma.durum', 0)
                    ->where('hatirlatma.netsiscari_id', $netsiscari)->where('hatirlatma.servis_id', $servis)->where('hatirlatma.depogelen_id',$depogelenid)
                    ->orderBy('id','desc')->first();
            else
                $hatirlatma_durum = Hatirlatma::select('hatirlatma.id')
                    ->where('hatirlatma.hatirlatmatip_id', $hatirlatmatip)->where('hatirlatma.tip', 2)->where('hatirlatma.durum', 0)
                    ->where('hatirlatma.netsiscari_id', $netsiscari)->where('hatirlatma.servis_id', $servis)
                    ->orderBy('id','desc')->first();
        if ($hatirlatma_durum) { //hatırlatma varsa uzerine ekler
            $hatirlatma_update = Hatirlatma::find($hatirlatma_durum->id);
            $hatirlatma_update->adet += $adet;
            $hatirlatma_update->kalan += $adet;
            $hatirlatma_update->save();
        } else { //hatırlatma yoksa yenisini oluşturur
            $hatirlatma_yeni = new Hatirlatma;
            $hatirlatma_yeni->hatirlatmatip_id = $hatirlatmatip;
            $hatirlatma_yeni->servisstokkodu = $stokkod ? $stokkod : 'SRV-00000';
            $hatirlatma_yeni->depogelen_id = $depogelenid ? $depogelenid : NULL;
            $hatirlatma_yeni->netsiscari_id = $netsiscari;
            $hatirlatma_yeni->servis_id = $servis;
            $hatirlatma_yeni->tarih = date('Y-m-d H:i:s');
            $hatirlatma_yeni->durum = 0;
            $hatirlatma_yeni->tip = 2;
            $hatirlatma_yeni->adet = $adet;
            $hatirlatma_yeni->kalan = $adet;
            $hatirlatma_yeni->save();
        }

    }

    public static function HatirlatmaGuncelle($hatirlatmatip,$netsiscari,$servis,$adet,$depogelenid=false,$stokkod=false)
    { //adet kadar işlem yapar
        if($stokkod)
            if($depogelenid)
                $hatirlatma = Hatirlatma::where('tip', 2)->where('durum', 0)->where('netsiscari_id', $netsiscari)
                    ->where('servis_id', $servis)->where('servisstokkodu',$stokkod)->where('depogelen_id',$depogelenid)
                    ->where('hatirlatmatip_id', $hatirlatmatip)->get();
            else
                $hatirlatma = Hatirlatma::where('tip', 2)->where('durum', 0)->where('netsiscari_id', $netsiscari)
                    ->where('servis_id', $servis)->where('servisstokkodu',$stokkod)
                    ->where('hatirlatmatip_id', $hatirlatmatip)->get();
        else
            if($depogelenid)
                $hatirlatma = Hatirlatma::where('tip', 2)->where('durum', 0)->where('netsiscari_id', $netsiscari)
                    ->where('servis_id', $servis)->where('depogelen_id',$depogelenid)
                    ->where('hatirlatmatip_id', $hatirlatmatip)->get();
            else
                $hatirlatma = Hatirlatma::where('tip', 2)->where('durum', 0)->where('netsiscari_id', $netsiscari)
                    ->where('servis_id', $servis)
                    ->where('hatirlatmatip_id', $hatirlatmatip)->get();
        if ($hatirlatma->count() > 0) {
            $count = $adet;
            foreach ($hatirlatma as $hatirlatma_update) {
                if ($count >= $hatirlatma_update->kalan) {
                    $count -= $hatirlatma_update->kalan;
                    $hatirlatma_update->durum = 1;
                    $hatirlatma_update->kalan = 0;
                    $hatirlatma_update->save();
                } else {
                    $hatirlatma_update->kalan -= $count;
                    $hatirlatma_update->save();
                    break;
                }
            }
        }
    }

    public static function HatirlatmaIdGuncelle($hatirlatmaid,$adet)
    { //adet kadar işlem yapar
        $hatirlatma = Hatirlatma::find($hatirlatmaid);
        if ($hatirlatma) {
            if($adet==$hatirlatma->kalan){
                $hatirlatma->durum = 1;
                $hatirlatma->kalan = 0;
                $hatirlatma->save();
            } else {
                $hatirlatma->kalan -= $adet;
                $hatirlatma->save();
            }
        }
    }

    public static function HatirlatmaGeriAl($hatirlatmatip,$netsiscari,$servis,$adet,$depogelenid=false,$stokkod=false)
    { //adet kadarlık işlemi geri alır
        if($stokkod)
            if($depogelenid)
                $hatirlatma = Hatirlatma::where('tip', 2)->where('netsiscari_id', $netsiscari)
                    ->where('servis_id', $servis)->where('servisstokkodu',$stokkod)->where('depogelen_id',$depogelenid)
                    ->where('hatirlatmatip_id', $hatirlatmatip)->orderBy('id','desc')->first();
            else
                $hatirlatma = Hatirlatma::where('tip', 2)->where('netsiscari_id', $netsiscari)
                    ->where('servis_id', $servis)->where('servisstokkodu',$stokkod)
                    ->where('hatirlatmatip_id', $hatirlatmatip)->orderBy('id','desc')->first();
        else
            if($depogelenid)
                $hatirlatma = Hatirlatma::where('tip', 2)->where('netsiscari_id', $netsiscari)
                    ->where('servis_id', $servis)->where('depogelen_id',$depogelenid)
                    ->where('hatirlatmatip_id', $hatirlatmatip)->orderBy('id','desc')->first();
            else
                $hatirlatma = Hatirlatma::where('tip', 2)->where('netsiscari_id', $netsiscari)
                    ->where('servis_id', $servis)
                    ->where('hatirlatmatip_id', $hatirlatmatip)->orderBy('id','desc')->first();
        if($hatirlatma)
        {
            $hatirlatma->durum=0;
            $hatirlatma->kalan+=$adet;
            $hatirlatma->save();
        }
    }

    public static function HatirlatmaSil($hatirlatmatip,$netsiscari,$servis,$adet,$depogelenid=false,$stokkod=false)
    { //adet kadar işlem siler
        try {
            if ($stokkod)
                if ($depogelenid)
                    $hatirlatma = Hatirlatma::where('tip', 2)->where('durum', 0)->where('netsiscari_id', $netsiscari)
                        ->where('servis_id', $servis)->where('servisstokkodu', $stokkod)->where('depogelen_id', $depogelenid)
                        ->where('hatirlatmatip_id', $hatirlatmatip)->get();
                else
                    $hatirlatma = Hatirlatma::where('tip', 2)->where('durum', 0)->where('netsiscari_id', $netsiscari)
                        ->where('servis_id', $servis)->where('servisstokkodu', $stokkod)
                        ->where('hatirlatmatip_id', $hatirlatmatip)->get();
            else
                if ($depogelenid)
                    $hatirlatma = Hatirlatma::where('tip', 2)->where('durum', 0)->where('netsiscari_id', $netsiscari)
                        ->where('servis_id', $servis)->where('depogelen_id', $depogelenid)
                        ->where('hatirlatmatip_id', $hatirlatmatip)->get();
                else
                    $hatirlatma = Hatirlatma::where('tip', 2)->where('durum', 0)->where('netsiscari_id', $netsiscari)
                        ->where('servis_id', $servis)
                        ->where('hatirlatmatip_id', $hatirlatmatip)->get();
            if ($hatirlatma->count() > 0) {
                $count = $adet;
                foreach ($hatirlatma as $hatirlatma_update) {
                    if ($hatirlatma_update->kalan > 0) {
                        if ($count >= $hatirlatma_update->kalan) {
                            $count -= $hatirlatma_update->kalan;
                            $hatirlatma_update->durum = 1;
                            $hatirlatma_update->adet -= $hatirlatma_update->kalan;
                            $hatirlatma_update->kalan = 0;
                            $hatirlatma_update->save();
                            if ($hatirlatma_update->adet == 0)
                                $hatirlatma_update->delete();
                        } else {
                            $hatirlatma_update->adet -= $count;
                            $hatirlatma_update->kalan -= $count;
                            $hatirlatma_update->save();
                            break;
                        }
                    } else {
                        $hatirlatma_update->kalan = 0;
                        $hatirlatma_update->durum = 1;
                        $hatirlatma_update->save();
                        if ($hatirlatma_update->adet == 0)
                            $hatirlatma_update->delete();
                    }
                }
            }
        } catch (Exception $e) {
            Log::error($e);
        }
    }

    public static function HatirlatmaDuzenle($hatirlatmatip,$eskicari,$yenicari,$servis,$adet,$durum=0,$eskidepogelen=false,$yenidepogelen=false,$eskistokkod=false,$yenistokkod=false)
    { //adet kadarlık işlemi değiştirir
        if($eskicari!=$yenicari || $eskidepogelen!=$yenidepogelen || $eskistokkod!=$yenistokkod){
            if($eskistokkod)
                if($eskidepogelen)
                    $hatirlatma = Hatirlatma::where('tip', 2)->where('netsiscari_id', $eskicari)
                        ->where('servis_id', $servis)->where('servisstokkodu',$eskistokkod)->where('depogelen_id',$eskidepogelen)
                        ->where('hatirlatmatip_id', $hatirlatmatip)->get();
                else
                    $hatirlatma = Hatirlatma::where('tip', 2)->where('netsiscari_id', $eskicari)
                        ->where('servis_id', $servis)->where('servisstokkodu',$eskistokkod)
                        ->where('hatirlatmatip_id', $hatirlatmatip)->get();
            else
                if($eskidepogelen)
                    $hatirlatma = Hatirlatma::where('tip', 2)->where('netsiscari_id', $eskicari)
                        ->where('servis_id', $servis)->where('depogelen_id',$eskidepogelen)
                        ->where('hatirlatmatip_id', $hatirlatmatip)->get();
                else
                    $hatirlatma = Hatirlatma::where('tip', 2)->where('netsiscari_id', $eskicari)
                        ->where('servis_id', $servis)
                        ->where('hatirlatmatip_id', $hatirlatmatip)->get();
            if ($hatirlatma->count() > 0) {
                if($durum==1){
                    $count = $adet;
                    foreach ($hatirlatma as $hatirlatma_update) {
                        if ($count >= $hatirlatma_update->adet) {
                            $count -= $hatirlatma_update->adet;
                            $hatirlatma_update->netsiscari_id = $yenicari;
                            $hatirlatma_update->depogelen_id=$yenidepogelen ? $yenidepogelen : NULL;
                            $hatirlatma_update->servisstokkodu=$yenistokkod ? $yenistokkod : 'SRV-00000';
                            $hatirlatma_update->save();
                        } else {
                            $hatirlatma_update->adet -= $count;
                            $hatirlatma_update->save();
                            $hatirlatma_yeni = Hatirlatma::where('tip', 2)->where('netsiscari_id', $yenicari)
                                ->where('servis_id', $servis)->where('servisstokkodu',$yenistokkod ? $yenistokkod : 'SRV-00000')
                                ->where('depogelen_id',$yenidepogelen ? $yenidepogelen : NULL)
                                ->where('hatirlatmatip_id', $hatirlatmatip)->where('durum',1)->first();
                            if($hatirlatma_yeni){
                                $hatirlatma_yeni->adet += $adet;
                                $hatirlatma_yeni->kalan = 0;
                                $hatirlatma_yeni->save();
                            }else{
                                $hatirlatma_yeni = new Hatirlatma;
                                $hatirlatma_yeni->hatirlatmatip_id = $hatirlatmatip;
                                $hatirlatma_yeni->servisstokkodu = $yenistokkod ? $yenistokkod : 'SRV-00000';
                                $hatirlatma_yeni->depogelen_id = $yenidepogelen ? $yenidepogelen : NULL;
                                $hatirlatma_yeni->netsiscari_id = $yenicari;
                                $hatirlatma_yeni->servis_id = $hatirlatma_update->servis_id;
                                $hatirlatma_yeni->tarih = $hatirlatma_update->tarih;
                                $hatirlatma_yeni->durum = 1;
                                $hatirlatma_yeni->tip = 2;
                                $hatirlatma_yeni->adet = $adet;
                                $hatirlatma_yeni->kalan = 0;
                                $hatirlatma_yeni->save();
                            }
                            break;
                        }
                    }
                }else{
                    $count = $adet;
                    foreach ($hatirlatma as $hatirlatma_update) {
                        if ($count >= $hatirlatma_update->kalan) {
                            $count -= $hatirlatma_update->kalan;
                            $hatirlatma_update->durum = 1;
                            $hatirlatma_update->adet -= $hatirlatma_update->kalan;
                            $hatirlatma_update->kalan = 0;
                            $hatirlatma_update->save();
                        } else {
                            $hatirlatma_update->adet -= $count;
                            $hatirlatma_update->kalan -= $count;
                            $hatirlatma_update->save();
                            break;
                        }
                    }
                    $hatirlatma_yeni = Hatirlatma::where('tip', 2)->where('netsiscari_id', $yenicari)
                        ->where('servis_id', $servis)->where('servisstokkodu',$yenistokkod ? $yenistokkod : 'SRV-00000')
                        ->where('depogelen_id',$yenidepogelen ? $yenidepogelen : NULL)
                        ->where('hatirlatmatip_id', $hatirlatmatip)->where('durum',0)->first();
                    if($hatirlatma_yeni){
                        $hatirlatma_yeni->adet += $adet;
                        $hatirlatma_yeni->kalan += $adet;
                        $hatirlatma_yeni->save();
                    }else{
                        $hatirlatma_yeni = new Hatirlatma;
                        $hatirlatma_yeni->hatirlatmatip_id = $hatirlatmatip;
                        $hatirlatma_yeni->servisstokkodu = $yenistokkod ? $yenistokkod : 'SRV-00000';
                        $hatirlatma_yeni->depogelen_id = $yenidepogelen ? $yenidepogelen : NULL;
                        $hatirlatma_yeni->netsiscari_id = $yenicari;
                        $hatirlatma_yeni->servis_id = $servis;
                        $hatirlatma_yeni->tarih = date('Y-m-d H:i:s');
                        $hatirlatma_yeni->durum = 0;
                        $hatirlatma_yeni->tip = 2;
                        $hatirlatma_yeni->adet = $adet;
                        $hatirlatma_yeni->kalan = $adet;
                        $hatirlatma_yeni->save();
                    }
                }
            }
        }
    }

    public static function BildirimEkle($hatirlatmatip,$netsiscari,$servis,$adet,$depogelenid=false,$stokkod=false)
    { //adet kadarlık bildirim açar
        if($stokkod)
            if($depogelenid)
                $bildirim_durum = Hatirlatma::select('hatirlatma.id')
                    ->where('hatirlatma.hatirlatmatip_id', $hatirlatmatip)->where('hatirlatma.tip', 1)->where('hatirlatma.durum', 0)
                    ->where('hatirlatma.netsiscari_id', $netsiscari)->where('hatirlatma.servis_id', $servis)->where('hatirlatma.depogelen_id',$depogelenid)
                    ->where('hatirlatma.servisstokkodu',$stokkod)->orderBy('id','desc')->first();
            else
                $bildirim_durum = Hatirlatma::select('hatirlatma.id')
                    ->where('hatirlatma.hatirlatmatip_id', $hatirlatmatip)->where('hatirlatma.tip', 1)->where('hatirlatma.durum', 0)
                    ->where('hatirlatma.netsiscari_id', $netsiscari)->where('hatirlatma.servis_id', $servis)
                    ->where('hatirlatma.servisstokkodu',$stokkod)->orderBy('id','desc')->first();
        else
            if($depogelenid)
                $bildirim_durum = Hatirlatma::select('hatirlatma.id')
                    ->where('hatirlatma.hatirlatmatip_id', $hatirlatmatip)->where('hatirlatma.tip', 1)->where('hatirlatma.durum', 0)
                    ->where('hatirlatma.netsiscari_id', $netsiscari)->where('hatirlatma.servis_id', $servis)->where('hatirlatma.depogelen_id',$depogelenid)
                    ->orderBy('id','desc')->first();
            else
                $bildirim_durum = Hatirlatma::select('hatirlatma.id')
                    ->where('hatirlatma.hatirlatmatip_id', $hatirlatmatip)->where('hatirlatma.tip', 1)->where('hatirlatma.durum', 0)
                    ->where('hatirlatma.netsiscari_id', $netsiscari)->where('hatirlatma.servis_id', $servis)
                    ->orderBy('id','desc')->first();
        if ($bildirim_durum) { //bildirim varsa uzerine ekler
            $bildirim_update = Hatirlatma::find($bildirim_durum->id);
            $bildirim_update->adet += $adet;
            $bildirim_update->save();
        } else { //bildirim yoksa yenisini oluşturur
            $bildirim_yeni = new Hatirlatma;
            $bildirim_yeni->hatirlatmatip_id = $hatirlatmatip;
            $bildirim_yeni->servisstokkodu = $stokkod ? $stokkod : 'SRV-00000';
            $bildirim_yeni->depogelen_id = $depogelenid ? $depogelenid : NULL;
            $bildirim_yeni->netsiscari_id = $netsiscari;
            $bildirim_yeni->servis_id = $servis;
            $bildirim_yeni->tarih = date('Y-m-d H:i:s');
            $bildirim_yeni->durum = 0;
            $bildirim_yeni->tip = 1;
            $bildirim_yeni->adet = $adet;
            $bildirim_yeni->kalan = 0;
            $bildirim_yeni->save();
        }
    }

    public static function BildirimGeriAl($hatirlatmatip,$netsiscari,$servis,$adet,$depogelenid=false,$stokkod=false)
    { //adet kadarlık işlemi geri alır
        try {
            if ($stokkod)
                if ($depogelenid)
                    $bildirim = Hatirlatma::where('tip', 1)->where('netsiscari_id', $netsiscari)
                        ->where('servis_id', $servis)->where('servisstokkodu', $stokkod)->where('depogelen_id', $depogelenid)
                        ->where('hatirlatmatip_id', $hatirlatmatip)->orderBy('id', 'desc')->get();
                else
                    $bildirim = Hatirlatma::where('tip', 1)->where('netsiscari_id', $netsiscari)
                        ->where('servis_id', $servis)->where('servisstokkodu', $stokkod)
                        ->where('hatirlatmatip_id', $hatirlatmatip)->orderBy('id', 'desc')->get();
            else
                if ($depogelenid)
                    $bildirim = Hatirlatma::where('tip', 1)->where('netsiscari_id', $netsiscari)
                        ->where('servis_id', $servis)->where('depogelen_id', $depogelenid)
                        ->where('hatirlatmatip_id', $hatirlatmatip)->orderBy('id', 'desc')->get();
                else
                    $bildirim = Hatirlatma::where('tip', 1)->where('netsiscari_id', $netsiscari)
                        ->where('servis_id', $servis)
                        ->where('hatirlatmatip_id', $hatirlatmatip)->orderBy('id', 'desc')->get();
            if ($bildirim->count() > 0) {
                $count = $adet;
                foreach ($bildirim as $bildirim_update) {
                    if ($count >= $bildirim_update->adet) {
                        $count -= $bildirim_update->adet;
                        $bildirim_update->delete();
                    } else {
                        $bildirim_update->adet -= $count;
                        $bildirim_update->save();
                        break;
                    }
                }
            }
        } catch (Exception $e) {
            Log::error($e);
        }
    }

    public static function BildirimDuzenle($hatirlatmatip,$eskicari,$yenicari,$servis,$adet,$eskidepogelen=false,$yenidepogelen=false,$eskistokkod=false,$yenistokkod=false)
    { //adet kadarlık bildirim düzenler
        if($eskicari!=$yenicari || $eskidepogelen!=$yenidepogelen || $eskistokkod!=$yenistokkod){
            if($eskistokkod)
                if($eskidepogelen)
                    $bildirim_durum = Hatirlatma::select('hatirlatma.id')
                        ->where('hatirlatma.hatirlatmatip_id', $hatirlatmatip)->where('hatirlatma.tip', 1)
                        ->where('hatirlatma.netsiscari_id', $eskicari)->where('hatirlatma.servis_id', $servis)->where('hatirlatma.depogelen_id',$eskidepogelen)
                        ->where('hatirlatma.servisstokkodu',$eskistokkod)->orderBy('id','desc')->first();
                else
                    $bildirim_durum = Hatirlatma::select('hatirlatma.id')
                        ->where('hatirlatma.hatirlatmatip_id', $hatirlatmatip)->where('hatirlatma.tip', 1)
                        ->where('hatirlatma.netsiscari_id', $eskicari)->where('hatirlatma.servis_id', $servis)
                        ->where('hatirlatma.servisstokkodu',$eskistokkod)->orderBy('id','desc')->first();
            else
                if($eskidepogelen)
                    $bildirim_durum = Hatirlatma::select('hatirlatma.id')
                        ->where('hatirlatma.hatirlatmatip_id', $hatirlatmatip)->where('hatirlatma.tip', 1)
                        ->where('hatirlatma.netsiscari_id', $eskicari)->where('hatirlatma.servis_id', $servis)->where('hatirlatma.depogelen_id',$eskidepogelen)
                        ->orderBy('id','desc')->first();
                else
                    $bildirim_durum = Hatirlatma::select('hatirlatma.id')
                        ->where('hatirlatma.hatirlatmatip_id', $hatirlatmatip)->where('hatirlatma.tip', 1)
                        ->where('hatirlatma.netsiscari_id', $eskicari)->where('hatirlatma.servis_id', $servis)
                        ->orderBy('id','desc')->first();
            if ($bildirim_durum) { //bildirim varsa uzerine düzenler
                $bildirim_update = Hatirlatma::find($bildirim_durum->id);
                if($bildirim_update->adet>$adet){
                    $bildirim_update->adet -= $adet;
                    $bildirim_update->save();
                }
                $bildirim_yeni = new Hatirlatma;
                $bildirim_yeni->hatirlatmatip_id = $hatirlatmatip;
                $bildirim_yeni->servisstokkodu = $yenistokkod ? $yenistokkod : 'SRV-00000';
                $bildirim_yeni->depogelen_id = $yenidepogelen ? $yenidepogelen : NULL;
                $bildirim_yeni->netsiscari_id = $yenicari;
                $bildirim_yeni->servis_id = $servis;
                $bildirim_yeni->tarih = date('Y-m-d H:i:s');
                $bildirim_yeni->durum = 1;
                $bildirim_yeni->tip = 1;
                $bildirim_yeni->adet = $adet;
                $bildirim_yeni->kalan = 0;
                $bildirim_yeni->save();
            } else { //bildirim yoksa yenisini oluşturur
                $bildirim_yeni = new Hatirlatma;
                $bildirim_yeni->hatirlatmatip_id = $hatirlatmatip;
                $bildirim_yeni->servisstokkodu = $yenistokkod ? $yenistokkod : 'SRV-00000';
                $bildirim_yeni->depogelen_id = $yenidepogelen ? $yenidepogelen : NULL;
                $bildirim_yeni->netsiscari_id = $yenicari;
                $bildirim_yeni->servis_id = $servis;
                $bildirim_yeni->tarih = date('Y-m-d H:i:s');
                $bildirim_yeni->durum = 1;
                $bildirim_yeni->tip = 1;
                $bildirim_yeni->adet = $adet;
                $bildirim_yeni->kalan = 0;
                $bildirim_yeni->save();
            }
        }
    }

    public static function KasaKaydet($bilgi,$type){
        if($type==1){ //sayaç satışı
            try {
                //$sayacsatis = SubeSayacSatis::find($id);
                $netsiscari = NetsisCari::find($bilgi->netsiscari_id);
                $kasa = new Kasa;
                $kasa->KSMAS_KOD = $bilgi->kasakodu;
                $kasa->TARIH = $bilgi->faturatarihi;
                $kasa->FISNO = $bilgi->faturano;
                $kasa->IO = 'G';
                $kasa->TIP = 'F';
                $kasa->ACIKLAMA = BackendController::ReverseTrk(mb_substr(trim($netsiscari->cariadi),0,
                    (strlen(utf8_decode(trim($netsiscari->cariadi)))>20 ? 20 : strlen(utf8_decode(trim($netsiscari->cariadi)))),'utf-8').'.FT.:'.$bilgi->faturano);
                $kasa->TUTAR = $bilgi->toplamtutar;
                $kasa->CARI_MUH = 'C';
                $kasa->KOD = BackendController::ReverseTrk($bilgi->carikod);
                $kasa->DOVIZTUT = 0;
                $kasa->KUR = 0;
                $kasa->PLASIYER_KODU = $bilgi->plasiyerkod;
                $kasa->KULL_ID = $bilgi->netsiskullanici_id;
                $kasa->SUBE_KODU = $bilgi->subekodu;
                $kasa->PROJE_KODU = $bilgi->projekodu;
                $kasa->KAYITYAPANKUL = $bilgi->netsiskullanici;
                $kasa->KAYITTARIHI = date('Y-m-d H:i:s');
                $kasa->KAYNAK = 0;
                $kasa->ENTEGREFKEY = '011' . $bilgi->faturano . BackendController::ReverseTrk($bilgi->carikod);
                $kasa->KRTSOZMASINCKEYNO = 0;
                $kasa->TAKSIT = 0;
                $kasa->GECERLI = 'E';
                $kasa->save();
                return 1;
            } catch (Exception $e) {
                Log::error($e);
                return 0;
            }
        }else{ //abone sayaç teslimi
            try {
                //$aboneteslim = AboneTeslim::find($id);
                $netsiscari = NetsisCari::find($bilgi->netsiscari_id);
                $kasa = new Kasa;
                $kasa->KSMAS_KOD = $bilgi->kasakodu;
                $kasa->TARIH = $bilgi->teslimtarihi;
                $kasa->FISNO = $bilgi->faturano;
                $kasa->IO = 'G';
                $kasa->TIP = 'F';
                $kasa->ACIKLAMA = BackendController::ReverseTrk(mb_substr(trim($netsiscari->cariadi),0,
                    (strlen(utf8_decode(trim($netsiscari->cariadi)))>20 ? 20 : strlen(utf8_decode(trim($netsiscari->cariadi)))),'utf-8').'.FT.:'.$bilgi->faturano);
                $kasa->TUTAR = $bilgi->toplamtutar;
                $kasa->CARI_MUH = 'C';
                $kasa->KOD = BackendController::ReverseTrk($bilgi->carikod);
                $kasa->DOVIZTUT = 0;
                $kasa->KUR = 0;
                $kasa->PLASIYER_KODU = $bilgi->plasiyerkod;
                $kasa->KULL_ID = $bilgi->netsiskullanici_id;
                $kasa->SUBE_KODU = $bilgi->subekodu;
                $kasa->PROJE_KODU = $bilgi->projekodu;
                $kasa->KAYITYAPANKUL = $bilgi->netsiskullanici;
                $kasa->KAYITTARIHI = date('Y-m-d H:i:s');
                $kasa->KAYNAK = 0;
                $kasa->ENTEGREFKEY = '011' . $bilgi->faturano . BackendController::ReverseTrk($bilgi->carikod);
                $kasa->KRTSOZMASINCKEYNO = 0;
                $kasa->TAKSIT = 0;
                $kasa->GECERLI = 'E';
                $kasa->save();
                return 1;
            } catch (Exception $e) {
                Log::error($e);
                return 0;
            }
        }
    }

    public static function KasaSil($bilgi,$type){
        if($type==1){ //sayaç satışı
            try {
                $kasa = Kasa::where('KSMAS_KOD',$bilgi->kasakodu)->where('FISNO',$bilgi->faturano)->where('IO','G')
                    ->where('TIP','F')->where('KOD',BackendController::ReverseTrk($bilgi->carikod))
                    ->where('SUBE_KODU',$bilgi->subekodu)->get();
                foreach ($kasa as $kasa_){
                    $kasa_->delete();
                }
                return 1;
            } catch (Exception $e) {
                Log::error($e);
                return 0;
            }
        }else{ //abone sayaç teslimi
            try {
                // $aboneteslim = AboneTeslim::find($id);
                $kasa = Kasa::where('KSMAS_KOD',$bilgi->kasakodu)->where('FISNO',$bilgi->faturano)->where('IO','G')
                    ->where('TIP','F')->where('KOD',BackendController::ReverseTrk($bilgi->carikod))
                    ->where('SUBE_KODU',$bilgi->subekodu)->get();
                foreach ($kasa as $kasa_){
                    $kasa_->delete();
                }
                return 1;
            } catch (Exception $e) {
                Log::error($e);
                return 0;
            }
        }
    }

    public static function AboneTeslimFaturasi($id){ // Netsis Tarafında Satış Faturasını Karşılar
        $sabit = SistemSabitleri::where('adi','NetsisFatura')->first();
        if($sabit->deger==0) //fatura kesilmeyecek
        {
            return 0;
        }else{
            try {
                $aboneteslim = AboneTeslim::find($id);
                $netsiscari = NetsisCari::find($aboneteslim->netsiscari_id);
                $vadegunu = is_null($netsiscari->vadegunu) ? 0 : $netsiscari->vadegunu;
                $odemetarih = date('Y-m-d', strtotime(date('Y-m-d') . ' + ' . $vadegunu . ' days'));
                $abone = Abone::find($aboneteslim->abone_id);
                $fatirsno = $aboneteslim->faturano;
                $secilenler = explode(',', $aboneteslim->secilenler);
                $arizafiyatlar = ArizaFiyat::whereIn('sayacgelen_id', $secilenler)->get();
                foreach ($arizafiyatlar as $arizafiyat) {
                    $depogelen = DepoGelen::find($arizafiyat->depogelen_id);
                    $arizafiyat->servisstokkodu = $depogelen->servisstokkodu;
                    $arizafiyat->adet = 1;
                }
                $arizafiyatlist = $arizafiyatlar->toArray();
                $fatura = Fatuirs::where('SUBE_KODU', $aboneteslim->subekodu)->where('FTIRSIP', 1)->where('FATIRS_NO', $fatirsno)->first();
                if ($fatura) {
                    return 6;
                }
                try {
                    $fatura = new Fatuirs;
                    $faturaek = new Fatuek;
                    $fatura->SUBE_KODU = $aboneteslim->subekodu;
                    $fatura->FATIRS_NO = $fatirsno;
                    $fatura->FTIRSIP = '1';
                    $fatura->CARI_KODU = BackendController::ReverseTrk($aboneteslim->carikod);
                    $fatura->TARIH = date('Y-m-d');
                    $fatura->TIPI = 1;
                    $fatura->ACIKLAMA = BackendController::ReverseTrk(mb_substr(trim($abone->adisoyadi),0,
                        (strlen(utf8_decode(trim($abone->adisoyadi)))>20 ? 20 : strlen(utf8_decode(trim($abone->adisoyadi)))),'utf-8'));
                    $fatura->KOD1 = 'F';
                    $fatura->ODEMEGUNU = $vadegunu;
                    $fatura->ODEMETARIHI = $odemetarih;
                    $fatura->KDV_DAHILMI = 'E';
                    $fatura->SIPARIS_TEST = date('Y-m-d');
                    $fatura->PLA_KODU = $aboneteslim->plasiyerkod;
                    $fatura->KS_KODU = $aboneteslim->kasakodu;
                    $fatura->C_YEDEK6 = 'X';
                    $fatura->D_YEDEK10 = date('Y-m-d');
                    $fatura->PROJE_KODU = $aboneteslim->projekodu;
                    $fatura->KAYITYAPANKUL = $aboneteslim->netsiskullanici;
                    $fatura->KAYITTARIHI = date('Y-m-d H:i:s');
                    $fatura->ISLETME_KODU = 1;
                    $fatura->GIB_FATIRS_NO = $fatirsno;
                    $fatura->SIST_EARSIVMI = 1;

                    $faturaek->SUBE_KODU = $aboneteslim->subekodu;;
                    $faturaek->FKOD = '1';
                    $faturaek->FATIRSNO = $fatirsno;
                    $faturaek->CKOD = BackendController::ReverseTrk($aboneteslim->carikod);
                    $faturaek->ACIK1 = $abone->tckimlikno;
                    $faturaek->ACIK2 = $abone->adisoyadi;
                    $faturaek->ACIK3 = $abone->telefon;
                    $faturaek->ACIK4 = $abone->faturaadresi;
                    $faturaek->ACIK8 = $aboneteslim->aciklama;
                    $faturaek->ACIK9 = $aboneteslim->odemesekli;
                    $faturaek->save();
                    $tutar = 0;
                    $kalemler = BackendController::FaturaGrupla($arizafiyatlist);
                    $i = 1;
                    $inckey = array();
                    try {
                        foreach ($kalemler as $kalem) {
                            $faturakalem = new Sthar;
                            $faturakalem->STOK_KODU = $kalem['servisstokkodu'];
                            $faturakalem->FISNO = $fatirsno;
                            $faturakalem->STHAR_GCMIK = $kalem['adet'];
                            $faturakalem->STHAR_GCKOD = 'C';
                            $faturakalem->STHAR_TARIH = date('Y-m-d');
                            $parabirimi = $kalem['parabirimi_id'];
                            if ($parabirimi != 1) {
                                $dovizkuru = DovizKuru::where('parabirimi_id', $parabirimi)->where('tarih', date('Y-m-d'))->first();
                                if (!$dovizkuru)
                                    return 5;
                                $kur = $dovizkuru->kurfiyati;
                                $doviztutar = $kalem['toplamtutar'];
                                $kalemtutar = $doviztutar * $kur;
                                $kalemkdv = round(($tutar * 18) / 118, 4);
                                $kalembrut = $kalemtutar - $kalemkdv;
                                $faturakalem->STHAR_NF = $kalembrut;
                                $faturakalem->STHAR_BF = $kalemtutar;
                                $faturakalem->STHAR_DOVTIP = $parabirimi;
                                $faturakalem->STHAR_DOVFIAT = $doviztutar;
                            } else {
                                $kalemtutar = $kalem['toplamtutar'];
                                $kalemkdv = round(($kalemtutar * 18) / 118, 4);
                                $kalembrut = $kalemtutar - $kalemkdv;
                                $faturakalem->STHAR_NF = $kalembrut;
                                $faturakalem->STHAR_BF = $kalemtutar;
                                $faturakalem->STHAR_DOVTIP = 0;
                                $faturakalem->STHAR_DOVFIAT = 0;
                            }
                            $faturakalem->STHAR_KDV = 18;
                            $faturakalem->DEPO_KODU = $aboneteslim->depokodu;
                            $faturakalem->STHAR_ACIKLAMA = BackendController::ReverseTrk($aboneteslim->carikod);
                            $faturakalem->STHAR_FTIRSIP = '1';
                            $faturakalem->LISTE_FIAT = 1;
                            $faturakalem->STHAR_HTUR = 'I';
                            $faturakalem->STHAR_ODEGUN = $vadegunu;
                            $faturakalem->STHAR_BGTIP = 'F';
                            $faturakalem->STHAR_KOD1 = 'F';
                            $faturakalem->STHAR_CARIKOD = BackendController::ReverseTrk($aboneteslim->carikod);
                            $faturakalem->PLASIYER_KODU = $aboneteslim->plasiyerkod;
                            $faturakalem->SIRA = $i;
                            $faturakalem->STRA_SIPKONT = 0;
                            $faturakalem->OLCUBR = 1;
                            $faturakalem->VADE_TARIHI = $odemetarih;
                            $faturakalem->SUBE_KODU = $aboneteslim->subekodu;;
                            $faturakalem->D_YEDEK10 = date('Y-m-d');
                            $faturakalem->PROJE_KODU = $aboneteslim->projekodu;
                            $tutar += ($faturakalem->STHAR_GCMIK * $faturakalem->STHAR_BF);
                            $i++;
                            $faturakalem->save();
                            $id = DB::connection('sqlsrv2')->select("select top(1)INCKEYNO from tblsthar where SUBE_KODU=" . $aboneteslim->subekodu . " and FISNO='" . $fatirsno . "' and STHAR_FTIRSIP='1' order by INCKEYNO desc");
                            $inckeyno = ($id[0]->INCKEYNO);
                            if (in_array($inckeyno, $inckey)) { //girilen inckey zaten ekliyse kaydedememiş demektir
                                return 7;
                            } else { //kaydetme başarılı
                                array_push($inckey, $inckeyno);
                            }
                        }
                    } catch (Exception $e) {
                        Log::error($e);
                        return 4;
                    }
                    $kdv = round(($tutar * 18) / 118, 2);
                    $brut = $tutar - $kdv;
                    $fatura->BRUTTUTAR = $brut; //toplam
                    $fatura->KDV = $kdv; //kdv si
                    $fatura->FATKALEM_ADEDI = ($i - 1); //kalem adedi
                    $fatura->GENELTOPLAM = $tutar; //genel toplam
                    $fatura->save();
                    $durum = BackendController::KasaKaydet($id, 2);
                    if ($durum != 1) {
                        return 8;
                    }
                    $durum=BackendController::ServisBilgisiEkle($aboneteslim,1); // Servis Bilgisi Eklenecek 0 Satış 1 Tamir sonrası servis 2 arıza kontrolü
                    if($durum==0){
                        return 9;
                    }else if($durum==2){
                        return 10;
                    }else if($durum==3){
                        return 11;
                    }else if($durum==4){
                        return 12;
                    }else{
                        return 1;
                    }
                } catch (Exception $e) {
                    Log::error($e);
                    return 2;
                }
            } catch (Exception $e) {
                Log::error($e);
                return 3;
            }
        }
    }

    public static function BildirimUpdate(){
        DB::beginTransaction();
        $date = date("Y-m-d", strtotime("-2 days")); // son 1 ay
        $bildirimler = Hatirlatma::where('tip', 1)->where('tarih','<',$date)->where('durum', 0)->get();
        foreach ($bildirimler as $bildirim) {
            $bildirim->durum = 1;
            $bildirim->gdurum = 'Okundu';
            $bildirim->save();
        }
        DB::commit();
    }

    public static function GirisAdiBelirle($netsiscariid,$uretimyerid){
        $netsiscari=NetsisCari::find($netsiscariid);
        $cariadi=str_replace(array("(",")","."),' ',mb_strtolower($netsiscari->cariadi,"UTF-8"));
        $cariadi=str_replace(array("-"),'',$cariadi);
        $list=explode(' ',$cariadi);
        $silinecekler=array();
        foreach ($list as $isim){
            if(strlen(Str::slug($isim))<=1){
                array_push($silinecekler,$isim);
            }
        }
        $yenilist = array_values(array_diff($list,$silinecekler));
        $count=count($yenilist);
        if($yenilist[0]!='manas'){
            if(strlen($yenilist[0])>7){
                $girisadi=Str::slug($yenilist[0]);
                $kullanici=Kullanici::where('girisadi',$girisadi)->first();
                if(!$kullanici)
                    return $girisadi;
                $random="";
                while($kullanici){
                    $random=$girisadi.rand(0,100);
                    $kullanici=Kullanici::where('girisadi',$random)->first();
                }
                return $random;
            }else if($count>1) {
                $girisadi = Str::slug($yenilist[0]) . '.' . Str::slug($yenilist[1]);
                $kullanici = Kullanici::where('girisadi', $girisadi)->first();
                if (!$kullanici)
                    return $girisadi;
                $random = "";
                while ($kullanici) {
                    $random = $girisadi . rand(0, 100);
                    $kullanici = Kullanici::where('girisadi', $random)->first();
                }
                return $random;
            }else{
                $girisadi=Str::slug($yenilist[0]);
                $kullanici=Kullanici::where('girisadi',$girisadi)->first();
                if(!$kullanici)
                    return $girisadi;
                $random="";
                while($kullanici){
                    $random=$girisadi.rand(0,100);
                    $kullanici=Kullanici::where('girisadi',$random)->first();
                }
                return $random;
            }
        }else{
            $uretimyer=UretimYer::find($uretimyerid);
            $yeradi=str_replace(array("(",")","."),' ',mb_strtolower($uretimyer->yeradi,"UTF-8"));
            $yeradi=str_replace(array("-"),'',$yeradi);
            $list=explode(' ',$yeradi);
            $silinecekler=array();
            foreach ($list as $isim){
                if(strlen(Str::slug($isim))<=1){
                    array_push($silinecekler,$isim);
                }
            }
            $yenilist = array_values(array_diff($list,$silinecekler));
            $count=count($yenilist);
            if(strlen($yenilist[0])>7){
                $girisadi=Str::slug($yenilist[0]);
                $kullanici=Kullanici::where('girisadi',$girisadi)->first();
                if(!$kullanici)
                    return $girisadi;
                $random="";
                while($kullanici){
                    $random=$girisadi.rand(0,100);
                    $kullanici=Kullanici::where('girisadi',$random)->first();
                }
                return $random;
            }else if($count>1) {
                $girisadi = Str::slug($yenilist[0]) . '.' . Str::slug($yenilist[1]);
                $kullanici = Kullanici::where('girisadi', $girisadi)->first();
                if (!$kullanici)
                    return $girisadi;
                $random = "";
                while ($kullanici) {
                    $random = $girisadi . rand(0, 100);
                    $kullanici = Kullanici::where('girisadi', $random)->first();
                }
                return $random;
            }else{
                $girisadi=Str::slug($yenilist[0]);
                $kullanici=Kullanici::where('girisadi',$girisadi)->first();
                if(!$kullanici)
                    return $girisadi;
                $random="";
                while($kullanici){
                    $random=$girisadi.rand(0,100);
                    $kullanici=Kullanici::where('girisadi',$random)->first();
                }
                return $random;
            }
        }
    }

    public static function PasswordResetLink($token){
        $passwordreset=PasswordReset::where('token',$token)->first();
        $link= URL::to('/').'/reminder/reset/'.$passwordreset->token;
        return $link;
    }

    public static function updateLastActive($kullanici_id){
        $kullanici=Kullanici::find($kullanici_id);
        $kullanici->last_active=date('Y-m-d H:i:s');
        $kullanici->save();
    }

    public static function KullaniciDurum($kullanici_id,$durum){
        $kullanici=Kullanici::find($kullanici_id);
        $kullanici->online=$durum;
        $kullanici->save();
    }

    public static function gelenMesaj($kullanici_id,$alici_id){
        $iletiler=Alici::where('kullanici_id',$kullanici_id)->get(array('ileti_id','son_okuma')); //kullanıcıya mesaj atılabilecek iletiler
        $count=0;
        foreach($iletiler as $ileti){
            $alici=Alici::where('kullanici_id',$alici_id)->where('ileti_id',$ileti->ileti_id)->first(array('ileti_id'));
            if($alici){ //alıcı da iletiye ekliyse
                $count += Mesaj::where('ileti_id',$alici->ileti_id)->where('kullanici_id',$alici_id)->where('updated_at','>',$ileti->son_okuma)->count();
            }
        }
        return $count;
    }

    public static function getMesajlar($kullanici_id,$alici_id){
        $iletiler=Alici::where('kullanici_id',$kullanici_id)->get(); //kullanıcıya mesaj atılabilecek iletiler
        $iletidurum=0; //mesaj sayısı 10 dan az ise 0 kalır
        foreach($iletiler as $ileti){
            $alici=Alici::where('kullanici_id',$alici_id)->where('ileti_id',$ileti->ileti_id)->first();
            if($alici){ //alıcı da iletiye ekliyse
                $mesajlar = Mesaj::where('ileti_id',$alici->ileti_id)->whereIn('kullanici_id',array($kullanici_id,$alici_id))->orderBy('id','desc')->get();
                if($mesajlar->count()>10){
                    $iletidurum=1;
                }
                $mesajlar=$mesajlar->take(10)->reverse();
                foreach($mesajlar as $mesaj){
                    $mesaj->kullanici=Kullanici::find($mesaj->kullanici_id);
                    if($mesaj->kullanici->avatar==null) $mesaj->kullanici->avatar='test.png';
                    $time = strtotime($mesaj->updated_at);
                    $mesaj->time=date('H:i',$time);
                    $ileti->son_okuma=new Carbon;
                    $ileti->save();
                }
                return Response::json(array('mesajlar' =>$mesajlar,'ileti'=>$alici->ileti_id,'iletidurum' =>$iletidurum));
            }
        }
        // ikili arasında mesajlaşma yoksa yenisini aç
        $ileti=new Ileti;
        $alici = Kullanici::find($alici_id);
        $ileti->konu=Auth::user()->adi_soyadi.' '.$alici->adi_soyadi;
        $ileti->save();
        Alici::firstOrCreate(
            [
                'ileti_id'     => $ileti->id,
                'kullanici_id'  => $alici_id
            ]
        );
        // Add replier as a participant
        $gonderen = Alici::firstOrCreate(
            [
                'ileti_id'     => $ileti->id,
                'kullanici_id'  => Auth::id()
            ]
        );
        $gonderen->son_okuma = new Carbon;
        $gonderen->save();
        return Response::json(array('mesajlar' => "",'ileti'=>$ileti->id,'iletidurum' =>$iletidurum));
    }

    public static function getSonmesajlar($kullanici_id,$alici_id){
        $iletiler=Alici::where('kullanici_id',$kullanici_id)->get(); //kullanıcıya mesaj atılabilecek iletiler
        foreach($iletiler as $ileti){
            $alici=Alici::where('kullanici_id',$alici_id)->where('ileti_id',$ileti->ileti_id)->first();
            if($alici){ //alıcı da iletiye ekliyse
                $mesajlar = Mesaj::where('ileti_id',$alici->ileti_id)->whereIn('kullanici_id',array($kullanici_id,$alici_id))->where('updated_at','>',$ileti->son_okuma)->get();
                foreach($mesajlar as $mesaj){
                    $mesaj->kullanici=Kullanici::find($mesaj->kullanici_id);
                    if($mesaj->kullanici->avatar==null) $mesaj->kullanici->avatar='test.png';
                    $time = strtotime($mesaj->updated_at);
                    $mesaj->time=date('H:i',$time);
                    $ileti->son_okuma=new Carbon;
                    $ileti->save();
                }
                return Response::json(array('mesajlar' =>$mesajlar));
            }
        }
        return Response::json(array('mesajlar' => ""));
    }

    public static function getSendmesaj(){
        $ileti_id=Input::get('iletiid');
        $aliciid=Input::get('aliciid');
        $mesaj=Input::get('text');
        if($ileti_id!=""){
            try {
                $ileti = Ileti::findOrFail($ileti_id);
            } catch (Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                return Response::json(array('durum' => "0",'error'=>$ileti_id.' numaralı İleti bulunamadı'));
            }
            $ileti->activateAllParticipants();
        }else{
            $ileti=new Ileti;
            $ileti->konu=Auth::user()->adi_soyadi;
            $ileti->save();
        }

        // Message
        Mesaj::create(
            [
                'ileti_id'      => $ileti->id,
                'kullanici_id'   => Auth::id(),
                'icerik'           => $mesaj,
            ]
        );

        // Add replier as a participant
        Alici::firstOrCreate(
            [
                'ileti_id'     => $ileti->id,
                'kullanici_id'  => $aliciid
            ]
        );
        // Add replier as a participant
        $gonderen = Alici::firstOrCreate(
            [
                'ileti_id'     => $ileti->id,
                'kullanici_id'  => Auth::id()
            ]
        );
        $gonderen->son_okuma = new Carbon;
        $gonderen->save();
        return Response::json(array('durum' => "1"));
    }

    public static function KullaniciBilgisi($kullanici_id){
        $netsiscarilist="";
        $yetkilinetsiscarilist="";
        $netsiscariarray=array();
        if(Auth::user()->grup_id=="17" || Auth::user()->grup_id=="19" || Auth::user()->grup_id=="6"){
            $netsiscariler=SubeYetkili::where('kullanici_id',$kullanici_id)->get(array('netsiscari_id'));
            foreach($netsiscariler as $netsiscari){
                $netsiscarilist.=($netsiscarilist=="" ? "" : "," ).$netsiscari->netsiscari_id;
                array_push($netsiscariarray,$netsiscari->netsiscari_id);
            }
            $yetkilinetsiscariler=Yetkili::where('kullanici_id',$kullanici_id)->where('aktif',1)->get(array('netsiscari_id'));
            foreach($yetkilinetsiscariler as $netsiscari){
                $yetkilinetsiscarilist.=($yetkilinetsiscarilist=="" ? "" : "," ).$netsiscari->netsiscari_id;
                array_push($netsiscariarray,$netsiscari->netsiscari_id);
            }
        }else if(Auth::user()->grup_id=="18"){
            $subepersonel=SubePersonel::where('kullanici_id',$kullanici_id)->first();
            if($subepersonel){
                $netsiscariler = Sube::where('subekodu',$subepersonel->subekodu)->get(array('netsiscari_id'));
                foreach($netsiscariler as $netsiscari){
                    $netsiscarilist.=($netsiscarilist=="" ? "" : "," ).$netsiscari->netsiscari_id;
                    array_push($netsiscariarray,$netsiscari->netsiscari_id);
                }
            }
        }
        $netsiscarilist.=($netsiscarilist=="" ? "" : ($yetkilinetsiscarilist=="" ? "" : "," )).$yetkilinetsiscarilist;
        if(Auth::user()->grup_id=="6" && $netsiscarilist==""){
            $netsiscari = NetsisCari::where('carikod','320-01-M026')->first();
            $netsiscarilist.=($netsiscarilist=="" ? "" : "," ).$netsiscari->id;
            array_push($netsiscariarray,$netsiscari->id);
        }
        Auth::user()->netsiscari_id=$netsiscariarray;
        Auth::user()->netsiscarilist=$netsiscarilist;

        $hatirlatmadurum = BackendController::getHatirlatmadurum(); //servisidleri getirir
        $hatirlatmatipdurum = BackendController::getHatirlatmatipdurum(); //hatırlatma tiplerini
        $bildirimtipdurum = BackendController::getBildirimtipdurum(); //bildirim tiplerini
        $onaybildirimtipdurum = BackendController::getOnaybildirimtipdurum(); //onay bildirim tiplerini
        $hatirlatma = BackendController::getHatirlatma($hatirlatmadurum,$hatirlatmatipdurum,$netsiscariarray);
        $bildirim = BackendController::getBildirim($hatirlatmadurum,$bildirimtipdurum,$netsiscariarray);
        $onaybildirim = BackendController::getBildirim($hatirlatmadurum,$onaybildirimtipdurum,$netsiscariarray);
        $hatirlatma->sayi = BackendController::getHatirlatmaSayi($hatirlatmadurum,$hatirlatmatipdurum,$netsiscariarray);
        $bildirim->sayi = BackendController::getBildirimSayi($hatirlatmadurum,$bildirimtipdurum,$netsiscariarray);
        $onaybildirim->sayi = BackendController::getBildirimSayi($hatirlatmadurum,$onaybildirimtipdurum,$netsiscariarray);
        /*$personeller = Kullanici::with('grup','alici')->where('id','<>',$kullanici_id)->where('grup_id','<',19)->orderBy('grup_id','asc')->get();
        foreach($personeller as $personel){
            $count=0;
            foreach($personel->alici as $ileti){
                $alici=Alici::where('kullanici_id',$personel->id)->where('ileti_id',$ileti->ileti_id)->first(array('ileti_id'));
                if($alici){ //alıcı da iletiye ekliyse
                    $count += Mesaj::where('ileti_id',$alici->ileti_id)->where('kullanici_id',$personel->id)->where('updated_at','>',$ileti->son_okuma)->count();
                }
            }
            $personel->gelenmesaj=$count; //BackendController::GelenMesaj($kullanici_id,$personel->id);
        }
        $musteriler = Kullanici::with('alici')->where('id','<>',$kullanici_id)->where('grup_id',19)->orderBy('adi_soyadi','asc')->get();
        foreach($musteriler as $musteri){
            $count=0;
            foreach($musteri->alici as $ileti){
                $alici=Alici::where('kullanici_id',$musteri->id)->where('ileti_id',$ileti->ileti_id)->first(array('ileti_id'));
                if($alici){ //alıcı da iletiye ekliyse
                    $count += Mesaj::where('ileti_id',$alici->ileti_id)->where('kullanici_id',$musteri->id)->where('updated_at','>',$ileti->son_okuma)->count();
                }
            }
            $musteri->gelenmesaj=$count;
            $yetkili = Yetkili::with('netsiscari')->where('kullanici_id',$musteri->id)->where('aktif',1)->first();
            if($yetkili){
                if($yetkili->netsiscari)
                    $musteri->cariadi=$yetkili->netsiscari->cariadi;
                else
                    $musteri->cariadi='';
            }else{
                $musteri->cariadi='';
            }
        }*/
        Auth::user()->hatirlatma = $hatirlatma;
        Auth::user()->bildirim = $bildirim;
        Auth::user()->onaybildirim = $onaybildirim;
        //Auth::user()->personeller = $personeller;
        //Auth::user()->musteriler = $musteriler;
    }

    public static function IslemEkle($type,$kullanici_id,$label,$icon,$aciklama,$ayrinti){
        $islem = new Islem;
        $islem->type=$type;
        $islem->aciklama=$aciklama;
        $islem->ayrinti=$ayrinti;
        $islem->label=$label;
        $islem->icon=$icon;
        $islem->kullanici_id=$kullanici_id;
        $islem->save();
    }

    public static function ServisBilgisiEkle($bilgi,$tipi){
        try {
            if($tipi==0){ //sayaç montaj
                //$sayacsatis = SubeSayacSatis::find($id);
                $abone = Abone::find($bilgi->abone_id);
                $sayaclar = explode(';', $bilgi->sayaclar);
                foreach ($sayaclar as $sayac) {
                    $sayaclist = explode(',', $sayac);
                    foreach ($sayaclist as $sayacid) {
                        if ($sayacid != 0) {
                            $abonesayac = AboneSayac::find($sayacid);
                            $abonetahsis = AboneTahsis::where('abone_id', $abone->id)->where('abonesayac_id', $abonesayac->id)->first();
                            if ($abonetahsis) {
                                $serviskayit = new ServisKayit;
                                $serviskayit->subekodu=$bilgi->subekodu;
                                $serviskayit->kayitadres = $abonesayac->adres;
                                $serviskayit->abonetahsis_id = $abonetahsis->id;
                                $serviskayit->netsiscari_id = $bilgi->netsiscari_id;
                                $serviskayit->uretimyer_id = $bilgi->uretimyer_id;
                                $serviskayit->kullanici_id = Auth::user()->id;
                                $serviskayit->tipi = $tipi;
                                $serviskayit->aciklama = $abonesayac->bilgi;
                                $serviskayit->acilmatarihi = $bilgi->faturatarihi;
                                $serviskayit->save();

                                BackendController::HatirlatmaEkle(13, $abone->netsiscari_id, 6, 1);
                                BackendController::BildirimEkle(13, $abone->netsiscari_id, 6, 1);
                            } else {
                                return 2;
                            }
                        }
                    }
                }
            }else{// arıza montaj
                //$aboneteslim = AboneTeslim::find($id);
                $abone = Abone::find($bilgi->abone_id);
                $secilenlist = explode(',',$bilgi->secilenler);
                foreach ($secilenlist as $sayacgelenid){
                    $sayacgelen = SayacGelen::find($sayacgelenid);
                    $abonesayac = AboneSayac::where('serino',$sayacgelen->serino)->first();
                    if(!$abonesayac){
                        return 3;
                    }
                    $abonetahsis = AboneTahsis::where('abone_id', $abone->id)->where('abonesayac_id', $abonesayac->id)->first();
                    if ($abonetahsis) {
                        $sube = Sube::where('subekodu',$abone->subekodu)->where('aktif',1)->first();
                        if(!$sube){
                            return 4;
                        }
                        $serviskayit = new ServisKayit;
                        $serviskayit->subekodu=$sube->subekodu;
                        $serviskayit->kayitadres = $abonesayac->adres;
                        $serviskayit->abonetahsis_id = $abonetahsis->id;
                        $serviskayit->netsiscari_id = $abone->netsiscari_id;
                        $serviskayit->uretimyer_id = $abone->uretimyer_id;
                        $serviskayit->kullanici_id = Auth::user()->id;
                        $serviskayit->tipi = $tipi;
                        $serviskayit->acilmatarihi = $bilgi->teslimtarihi;
                        $serviskayit->takilmatarihi = $sayacgelen->takilmatarihi;
                        $serviskayit->ilkendeks = $sayacgelen->endeks;
                        $serviskayit->save();

                        BackendController::HatirlatmaEkle(13, $abone->netsiscari_id, 6, 1);
                        BackendController::BildirimEkle(13, $abone->netsiscari_id, 6, 1);
                    } else {
                        return 2;
                    }
                }
            }
            return 1;
        } catch (Exception $e) {
            Log::error($e);
            return 0;
        }
    }

    public static function SeriKaydet($sayacsatis,$inckey){
        try {
            $status = 0;
            $eklenenler = array();
            $netsiscari = NetsisCari::find($sayacsatis->netsiscari_id);
            $secilenlist = explode(',', $sayacsatis->secilenler);
            $sayaclist = explode(';', $sayacsatis->sayaclar);
            for ($i = 0; $i < count($secilenlist); $i++) {
                $secilen = SubeUrun::find($secilenlist[$i]);
                $stokkodu = NetsisStokKod::find($secilen->netsisstokkod_id);
                $fatkalem = Sthar::find($inckey[$i]);
                if ($secilen->baglanti) {
                    $abonesayaclist = explode(',', $sayaclist[$i]);
                    $sayaclar = AboneSayac::whereIn('id', $abonesayaclist)->get();
                    $fatura = Fatuirs::where('SUBE_KODU', $fatkalem->SUBE_KODU)->where('FTIRSIP', $fatkalem->STHAR_FTIRSIP)
                        ->where('FATIRS_NO', $fatkalem->FISNO)->first();
                    if($fatura->TIPI==2) // AÇIK FATURA
                        $status = 1;
                    foreach ($sayaclar as $sayac) {
                        try {
                            $seritra = new Seritra;
                            $seritra->KAYIT_TIPI = 'A';
                            $seritra->SERI_NO = $sayac->serino;
                            $seritra->STOK_KODU = $stokkodu->kodu;
                            $seritra->STRA_INC = $fatkalem->INCKEYNO;
                            $seritra->TARIH = $sayacsatis->faturatarihi;
                            $seritra->ACIK1 = '';
                            $seritra->ACIK2 = '';
                            $seritra->GCKOD = 'C';
                            $seritra->MIKTAR = 1;
                            $seritra->MIKTAR2 = 0;
                            $seritra->BELGENO = $fatkalem->FISNO;
                            $seritra->BELGETIP = $status ? 'J' : 'I';
                            $seritra->HARACIK = BackendController::ReverseTrk($netsiscari->carikod);
                            $seritra->SUBE_KODU = $sayacsatis->subekodu;
                            $seritra->DEPOKOD = $fatkalem->depokodu;
                            $seritra->SIPNO = '';
                            $seritra->KARSISERI = '';
                            $seritra->YEDEK1 = '';
                            $seritra->YEDEK4 = 0;
                            $seritra->ONAYTIPI = 'A';
                            $seritra->ONAYNUM = 0;
                            $seritra->ACIK3 = '';
                            $seritra->save();
                            $sira = DB::connection('sqlsrv2')->select("select top(1)SIRA_NO from tblseritra where SUBE_KODU=" . $sayacsatis->subekodu . " and STRA_INC=" . $fatkalem->INCKEYNO . " order by SIRA_NO desc");
                            $sirano = ($sira[0]->SIRA_NO);
                            if (in_array($sirano, $eklenenler)) { //gelen sırano zaten ekliyse kaydedememiş demektir
                                return 0;
                            } else { //kaydetme başarılı
                                array_push($eklenenler, $sirano);
                            }
                        } catch (Exception $e) {
                            Log::error($e);
                            return 0;
                        }
                    }
                }
            }
            return 1;
        } catch (Exception $e) {
            Log::error($e);
            return 0;
        }
    }

    public static function SeriSil($sayacsatis,$inckey){
        try {
            $secilenlist = explode(',', $sayacsatis->secilenler);
            $sayaclist = explode(';', $sayacsatis->sayaclar);
            for ($i = 0; $i < count($secilenlist); $i++) {
                $secilen = SubeUrun::find($secilenlist[$i]);
                $stokkodu = NetsisStokKod::find($secilen->netsisstokkod_id);
                if ($secilen->baglanti) {
                    $abonesayaclist = explode(',', $sayaclist[$i]);
                    $sayaclar = AboneSayac::whereIn('id', $abonesayaclist)->get();
                    foreach ($sayaclar as $sayac) {
                        try {
                            $seritra = Seritra::where('SERI_NO',$sayac->serino)->where('STOK_KODU',$stokkodu->kodu)->where('STRA_INC',$inckey[$i])->first();
                            if($seritra){
                                $seritra->delete();
                            }
                        } catch (Exception $e) {
                            Log::error($e);
                            return 0;
                        }
                    }
                }
            }
            return 1;
        } catch (Exception $e) {
            Log::error($e);
            return 0;
        }
    }

    public static function SayacDurum($serino,$uretimyerid,$servisid,$subedurum=false,$servistakipid=false,$sayacadiid=false){
        if ($servisid == 6) {
            if ($servistakipid) {
                $servistakip = ServisTakip::where('serino', $serino)->where('uretimyer_id',$uretimyerid)->where('servis_id', $servisid)->where('id', '<>', $servistakipid)
                    ->where(function ($query) {
                        $query->whereNull('onaylanan_id');
                    })->first();
            }else{
                $servistakip = ServisTakip::where('serino', $serino)->where('uretimyer_id',$uretimyerid)->where('servis_id', $servisid)
                    ->where(function ($query) {
                        $query->whereNull('onaylanan_id');
                    })->first();
            }
        }else{
            if($subedurum) { //şubelere ait sayaçlar
                if ($servistakipid) {
                    $servistakip = ServisTakip::where('serino', $serino)->where('uretimyer_id',$uretimyerid)->where('servis_id', $servisid)->where('id', '<>', $servistakipid)
                        ->where(function ($query) {
                            $query->whereNull('aboneteslim_id');
                        })->first();
                } else {
                    $servistakip = ServisTakip::where('serino', $serino)->where('uretimyer_id',$uretimyerid)->where('servis_id', $servisid)
                        ->where(function ($query) {
                            $query->whereNull('aboneteslim_id');
                        })->first();
                }
            }else{ //diğer aboneler
                if ($servistakipid) {
                    $servistakip = ServisTakip::where('serino', $serino)->where('uretimyer_id',$uretimyerid)->where('servis_id', $servisid)->where('id', '<>', $servistakipid)
                        ->where(function ($query) {
                            $query->whereNull('depoteslim_id');
                        })->first();
                }  else {
                    $servistakip = ServisTakip::where('serino', $serino)->where('uretimyer_id',$uretimyerid)->where('servis_id', $servisid)
                        ->where(function ($query) {
                            $query->whereNull('depoteslim_id');
                        })->first();
                }
            }
        }

        if($servistakip) {
            if ($sayacadiid) { // iç üniteler ile beraber sayacı da aynı anda geldiğinde kayıt yapabilemek için eklendi
                $sayacadi=SayacAdi::find($sayacadiid);
                $servistakip->sayacadi=SayacAdi::find($servistakip->sayacadi_id);
                if($sayacadi->sayacadi=='İÇ ÜNİTE' || $servistakip->sayacadi->sayacadi=='İÇ ÜNİTE'){
                    return false;
                }else{
                    return true;
                }
            } else {
                return true;
            }
        }else{
            return false;
        }
    }

    public static function DepoTeslimEkle($servistakip_id){
        $servistakip=ServisTakip::find($servistakip_id);
        $subeservistakip=ServisTakip::find($servistakip->subetakip_id);
        //sube için arizakayıt arizafiyat eklenecek
        $arizakayit=ArizaKayit::find($servistakip->arizakayit_id);
        $yeniarizakayit=$arizakayit->replicate();
        $yeniarizakayit->depogelen_id=$subeservistakip->depogelen_id;
        $yeniarizakayit->sayacgelen_id=$subeservistakip->sayacgelen_id;
        $yeniarizakayit->save();
        $arizafiyat=ArizaFiyat::find($servistakip->arizafiyat_id);
        $yeniarizafiyat=$arizafiyat->replicate();
        $yeniarizafiyat->arizakayit_id=$yeniarizakayit->id;
        $yeniarizafiyat->depogelen_id=$subeservistakip->depogelen_id;
        $yeniarizafiyat->sayacgelen_id=$subeservistakip->sayacgelen_id;
        $yeniarizafiyat->durum=0;
        $yeniarizafiyat->save();

        $subeservistakip->arizakayit_id=$yeniarizakayit->id;
        $subeservistakip->arizafiyat_id=$yeniarizafiyat->id;
        $subeservistakip->arizakayittarihi=$yeniarizakayit->arizakayittarihi;
        $subeservistakip->sonislemtarihi=$yeniarizakayit->arizakayittarihi;
        $subeservistakip->kullanici_id=$yeniarizakayit->arizakayit_kullanici_id;
        $subeservistakip->durum=2;
        $subeservistakip->save();
    }

    public static function DepoCikisEkle($servistakip_id){
        $servistakip=ServisTakip::find($servistakip_id);
        $subeservistakip=ServisTakip::find($servistakip->subetakip_id);
        //sube için arizakayıt arizafiyat hurda eklenecek
        $arizakayit=ArizaKayit::find($servistakip->arizakayit_id);
        $yeniarizakayit=$arizakayit->replicate();
        $yeniarizakayit->depogelen_id=$subeservistakip->depogelen_id;
        $yeniarizakayit->sayacgelen_id=$subeservistakip->sayacgelen_id;
        $yeniarizakayit->save();
        $arizafiyat=ArizaFiyat::find($servistakip->arizafiyat_id);
        $yeniarizafiyat=$arizafiyat->replicate();
        $yeniarizafiyat->arizakayit_id=$yeniarizakayit->id;
        $yeniarizafiyat->depogelen_id=$subeservistakip->depogelen_id;
        $yeniarizafiyat->sayacgelen_id=$subeservistakip->sayacgelen_id;
        $yeniarizafiyat->save();
        $hurdakayit=Hurda::find($servistakip->hurda_id);
        $yenihurdakayit=$hurdakayit->replicate();
        $yenihurdakayit->sayacgelen_id=$subeservistakip->sayacgelen_id;
        $yenihurdakayit->arizakayit_id=$yeniarizakayit->id;
        $yenihurdakayit->arizafiyat_id=$yeniarizafiyat->id;
        $yenihurdakayit->save();

        $subeservistakip->arizakayit_id=$yeniarizakayit->id;
        $subeservistakip->arizafiyat_id=$yeniarizafiyat->id;
        $subeservistakip->hurda_id=$yenihurdakayit->id;
        $subeservistakip->arizakayittarihi=$yeniarizakayit->arizakayittarihi;
        $subeservistakip->hurdalamatarihi=$yenihurdakayit->hurdatarihi;
        $subeservistakip->sonislemtarihi=$yeniarizakayit->arizakayittarihi;
        $subeservistakip->kullanici_id=$yeniarizafiyat->kullanici_id;
        $subeservistakip->durum=11;
        $subeservistakip->save();
    }

    public static function HurdaKayitEkle($servistakip_id){
        $servistakip=ServisTakip::find($servistakip_id);
        $subeservistakip=ServisTakip::find($servistakip->subetakip_id);
        //sube için arizakayıt arizafiyat hurda eklenecek
        $arizakayit=ArizaKayit::find($servistakip->arizakayit_id);
        $yeniarizakayit=$arizakayit->replicate();
        $yeniarizakayit->depogelen_id=$subeservistakip->depogelen_id;
        $yeniarizakayit->sayacgelen_id=$subeservistakip->sayacgelen_id;
        $yeniarizakayit->save();
        $arizafiyat=ArizaFiyat::find($servistakip->arizafiyat_id);
        $yeniarizafiyat=$arizafiyat->replicate();
        $yeniarizafiyat->arizakayit_id=$yeniarizakayit->id;
        $yeniarizafiyat->depogelen_id=$subeservistakip->depogelen_id;
        $yeniarizafiyat->sayacgelen_id=$subeservistakip->sayacgelen_id;
        $yeniarizafiyat->save();
        $hurdakayit=Hurda::find($servistakip->hurda_id);
        $yenihurdakayit=$hurdakayit->replicate();
        $yenihurdakayit->sayacgelen_id=$subeservistakip->sayacgelen_id;
        $yenihurdakayit->arizakayit_id=$yeniarizakayit->id;
        $yenihurdakayit->arizafiyat_id=$yeniarizafiyat->id;
        $yenihurdakayit->save();

        $subeservistakip->arizakayit_id=$yeniarizakayit->id;
        $subeservistakip->arizafiyat_id=$yeniarizafiyat->id;
        $subeservistakip->hurda_id=$yenihurdakayit->id;
        $subeservistakip->arizakayittarihi=$yeniarizakayit->arizakayittarihi;
        $subeservistakip->hurdalamatarihi=$yenihurdakayit->hurdatarihi;
        $subeservistakip->sonislemtarihi=$yeniarizakayit->arizakayittarihi;
        $subeservistakip->kullanici_id=$yeniarizafiyat->kullanici_id;
        $subeservistakip->durum=11;
        $subeservistakip->save();
    }

    public static function BeyannameNo($beyannameno,$inc){
        if($beyannameno==""){
            return "";
        }
        preg_match('/\d+$/', $beyannameno, $firstdigit);
        if(isset($firstdigit[0])){
            if(strlen($firstdigit[0])>3){
                return "";
            }
            $digitlength=0;
            $pattern="%0".$digitlength."d";
            return preg_replace_callback( '/(\\d+$)/', function($match) use($pattern,$inc) { return (sprintf($pattern,($match[0]+$inc))); }, $beyannameno );
        }
        return "";
    }

    public static function ServisTakipDurum($sayacgelen_id){
        $servistakip=ServisTakip::where('sayacgelen_id',$sayacgelen_id)->first();
        if($servistakip){
            switch ($servistakip->durum){
                case 1: return "Servis Sayaç Kayıdı Yapıldı";
                case 2: return "Arıza Kayıdı Yapıldı";
                case 3: return "Fiyatlandırma Yapıldı";
                case 4: return "Onay Formu Gönderildi";
                case 5: return "Müşteri Onayı Alındı";
                case 6: return "Fiyatlandırma Reddedildi";
                case 7: return "Tekrar Fiyatlandırıldı";
                case 8: return "Kalibrasyonu Yapıldı";
                case 9: return "Depoya Teslim Edildi";
                case 10: return "Geri Gönderildi";
                case 11: return "Hurdaya Ayrıldı";
                case 12: return "Depolararası Gönderildi";
                case 13: return "Aboneye Teslim Edildi";
                default: return "Diğer";
            }
        }else{
            return "";
        }
    }

    public static function BeyannameGuncelle($sayacadi_id,$durum){
        try {
            DB::beginTransaction();
            $sayacadi = SayacAdi::find($sayacadi_id);
            $aktifdurum = $durum ? 0 : -1; // 0 ise dahil olacak -1 ise dahil olmayacak
            $sayacgelenler=SayacGelen::where('sayacadi_id',$sayacadi->id)->whereIn('beyanname',array(0,-1))->get();
            foreach ($sayacgelenler as $sayacgelen){
                $sayacgelen->beyanname=$aktifdurum;
                $sayacgelen->save();
            }
            DB::commit();
            return 1;
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            return 0;
        }
    }

    public static function getSayacbilgiguncelle(){
        try {
            DB::beginTransaction();
            DB::insert("INSERT INTO sayac([serino],[cihazno],[uretimtarihi],[uretimyer_id])
              SELECT cast(SAYBIL.SERI_NO as nvarchar(15)) ,cast(SAYBIL.CIHAZ_NO as nvarchar(15)),SAYBIL.URETIM_TARIHI,[uretimyer].id
              FROM [MANAS]..[UPGEWOS_MANAS].[PGEWOS_TB_SAYBIL] [SAYBIL],[ServisTakip].[dbo].[uretimyer] [uretimyer]
              WHERE SAYBIL.SERI_NO > 0 AND cast(SAYBIL.SERI_NO as nvarchar(15)) NOT IN
              (SELECT [serino] FROM [ServisTakip].[dbo].[sayac])
              AND [uretimyer].oracle_id= SAYBIL.URTYER_ID
              ORDER BY SAYBIL.SERI_NO ");
            DB::commit();
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            return false;
        }
        return true;
    }

    public static function getPeriyodikKayitDurum($depogelenid){
        $sayacgelen = SayacGelen::where('depogelen_id',$depogelenid)->where('kalibrasyon',1)->count();
        if ($sayacgelen>0) {
            return 0;
        }else{
            return 1;
        }
    }

    public static function DepoDurumGuncelle($depogelenid){
        $depogelen=DepoGelen::find($depogelenid);
        $servistakip=ServisTakip::where('depogelen_id',$depogelenid)->get();
        if($servistakip->count()>=$depogelen->adet){
            $depogelen->kayitdurum=1;
            $depogelen->save();
        }
    }

    public static function Sort($list)
    {
        $liste="";
        if($list!=""){

            $elemanlar = explode(',',$list);
            sort($elemanlar);
            foreach ($elemanlar as $eleman){
                $liste.=($liste=="" ? "" : ",").$eleman;
            }
        }
        return $liste;
    }

    public static function Trk($string){
        $string = str_replace('Ð','Ğ',$string);
        $string = str_replace('Þ','Ş',$string);
        $string = str_replace('Ý','İ',$string);
        $string = str_replace('ð','ğ',$string);
        $string = str_replace('þ','ş',$string);
        $string = str_replace('ý','ı',$string);
        return $string;
    }

    public static function ReverseTrk($string){
        $string = str_replace('Ğ','Ð',$string);
        $string = str_replace('Ş','Þ',$string);
        $string = str_replace('İ','Ý',$string);
        $string = str_replace('ğ','ð',$string);
        $string = str_replace('ş','þ',$string);
        $string = str_replace('ı','ý',$string);
        return $string;
    }

    public static function Efatura($dbname,$netsiscari_id){
        $yil = filter_var($dbname, FILTER_SANITIZE_NUMBER_INT);
        $netsiscari=NetsisCari::find($netsiscari_id);
        if($yil<2016){
            return 0;
        }else{
            $efatura = EfaturaCari::where('SIRKET',$dbname)->where('CARI_KODU',BackendController::ReverseTrk($netsiscari->carikod))->first();
        }
        if($efatura)
            return 1;
        else
            return 0;
    }

    public static function AboneNoBul($sube,$adisoyadi,$tckimlikno,$serinolist){
        $abonebilgi=AboneBilgi::where('subekodu',$sube->subekodu)->where(function($query) use($adisoyadi,$tckimlikno,$serinolist){$query->where('adisoyadi','LIKE','%'.$adisoyadi.'%')
            ->orWhereIn('serino',$serinolist)->orWhere('vno',$tckimlikno);})->orderBy('abostsid','desc')->get();
        if($abonebilgi->count()>0){
            return $abonebilgi->first();
        }else{
            return false;
        }
    }

    function titleCase($string, $delimiters = array(" ", "-", ".", "'", "O'", "Mc"), $exceptions = array("and", "to", "of", "das", "dos", "I", "II", "III", "IV", "V", "VI"))
    {
        $string = mb_convert_case($string, MB_CASE_TITLE, "UTF-8");
        foreach ($delimiters as $dlnr => $delimiter) {
            $words = explode($delimiter, $string);
            $newwords = array();
            foreach ($words as $wordnr => $word) {
                if (in_array(mb_strtoupper($word, "UTF-8"), $exceptions)) {
                    // check exceptions list for any words that should be in upper case
                    $word = mb_strtoupper($word, "UTF-8");
                } elseif (in_array(mb_strtolower($word, "UTF-8"), $exceptions)) {
                    // check exceptions list for any words that should be in upper case
                    $word = mb_strtolower($word, "UTF-8");
                } elseif (!in_array($word, $exceptions)) {
                    // convert to uppercase (non-utf8 only)
                    $word = ucfirst($word);
                }
                array_push($newwords, $word);
            }
            $string = join($delimiter, $newwords);
        }//foreach
        return $string;
    }

    public static function StrtoUpper($text)
    {
        $search=array("ç","i","ı","ğ","ö","ş","ü");
        $replace=array("Ç","İ","I","Ğ","Ö","Ş","Ü");
        $text=str_replace($search,$replace,$text);
        $text=mb_strtoupper($text, "UTF-8");
        return $text;
    }

    public static function StrNormalized($text)
    {
        $search=array("ç","ı","ğ","ö","ş","ü","Ç","İ","Ğ","Ö","Ş","Ü");
        $replace=array("c","i","g","o","s","u","C","I","G","O","S","U");
        $text=str_replace($search,$replace,$text);
        $text=mb_strtolower($text, "UTF-8");
        return $text;
    }

    public static function Subesayackayitekle($kayit)
    {
        try {
            $gelistarih = date("Y-m-d");
            $uretimyeri = $kayit->uretimyer_id;
            $netsiscari_id = $kayit->netsiscari_id;
            $abonetahsis = AboneTahsis::find($kayit->abonetahsis_id);
            $abonesayac = AboneSayac::find($abonetahsis->abonesayac_id);
            $serino = $abonesayac->serino;
            $sayacparca = SayacParca::where('sayacadi_id', $abonesayac->sayacadi_id)->where('sayaccap_id', $abonesayac->sayaccap_id)->first();
            if (!$sayacparca) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Sayaca Ait Stok Kodu Bulunamadı.', 'text' => 'Sayacın Adının Sistemde Tanımlı Olduğundan Emin Olun!', 'type' => 'error'));
            }
            $serviskod = ServisStokKod::find($sayacparca->servisstokkod_id);
            $sokulmenedeni = $kayit->aciklama;
            $servissayac = $kayit->servissayacno == 0 ? null : $kayit->servissayacno;
            $ilkendeks = $kayit->ilkendeks;
            $netsiscari = NetsisCari::find($netsiscari_id);
            $subeyetkili = SubeYetkili::where('kullanici_id', Auth::user()->id)->where('aktif', 1)->first();
            if (!$subeyetkili) {
                $subeyetkili = SubeYetkili::where('subekodu', $kayit->subekodu)->where('aktif', 1)->first();
                if (!$subeyetkili) {
                    Input::flash();
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kullanıcı Yetkili Değil', 'text' => 'Kullanıcı Şube için Yetkili Olarak Eklenmemiş!', 'type' => 'error'));
                }
            }
            if (BackendController::SayacDurum($serino, $uretimyeri, 6, false, false, $abonesayac->sayacadi_id)) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Seri No Hatası', 'text' => $serino . ' Nolu Sayacın Depo Çıkışı Yapılmamış.Önce Sayaca Ait Diğer İşlemleri Bitiriniz.', 'type' => 'error'));
            }
            $sayaclar = BackendController::SubeDepoGirisGrupla(array($serino), array($uretimyeri), array($serviskod), array($abonesayac->sayacadi_id), array($abonesayac->sayaccap_id), array($sokulmenedeni), array($servissayac), array($ilkendeks));
            if (count($sayaclar) == 0) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Depo Sayaç Girişi Kaydedilemedi', 'text' => 'Girilen Sayaçların Seri Numaraları yazılmamış', 'type' => 'error'));
            }
            DB::beginTransaction();
            try {
                $faturano = Fatuno::where('SUBE_KODU', $subeyetkili->subekodu)->where('SERI', 'Z')->where('TIP', '9')->first();
                if (!$faturano) {
                    $faturano = new Fatuno;
                    $faturano->SUBE_KODU = $subeyetkili->subekodu;
                    $faturano->SERI = 'Z';
                    $faturano->TIP = 9;
                    $faturano->NUMARA = 'Z' . '0';
                    $faturano->save();
                }
                $belgeno = BackendController::FaturaNo($faturano->NUMARA, 1);
                Fatuno::where(['SUBE_KODU' => $subeyetkili->subekodu, 'SERI' => 'Z', 'TIP' => '9'])->update(['NUMARA' => $belgeno]);
            } catch (Exception $e) {
                DB::rollBack();
                Log::error($e);
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Fatura Numarası Kaydedilemedi', 'text' => 'Girilen Sayaçlar için Fatura Numarası Alınamadı', 'type' => 'error'));
            }
            $depogiris = BackendController::NetsisSubeDepoGiris($sayaclar, $gelistarih, $netsiscari_id, $belgeno);
            if ($depogiris['durum'] == '0') {
                DB::rollBack();
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Ambar Girişi Kaydedilemedi', 'text' => $depogiris['text'], 'type' => 'error'));
            }
            $adet = 0;
            $eklenenler = "";
            $faturakalemler = $depogiris['faturakalemler'];
            try {
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
                        $servisid = $depogelen->servis_id;
                        $depogelen->kod = ServisStokKod::where('stokkodu', $depogelen->servisstokkodu)->first();
                        foreach ($sayaclar as $sayacgrup) {
                            if ($sayacgrup['kod'] == $depogelen->kod->id) {
                                $biten = 0;
                                $secilenler = "";
                                $sayac = $sayacgrup['sayac']; //eklenecek sayaçlar
                                foreach ($sayac as $girilecek) {
                                    $serino = $girilecek['serino'];
                                    $sayacadi = $girilecek['sayacadi'];
                                    $sayaccap = $girilecek['sayaccap'];
                                    $uretimyeri = $girilecek['uretimyeri'];
                                    $sokulmenedeni = $girilecek['neden'];
                                    $servissayac = $girilecek['servissayac'];
                                    $endeks = $girilecek['endeks'];
                                    if ($serino != "") {
                                        try {
                                            $sayacgelen = new SayacGelen;
                                            $sayacgelen->depogelen_id = $depogelen->id;
                                            $sayacgelen->netsiscari_id = $netsiscari_id;
                                            $sayacgelen->stokkodu = $depogelen->servisstokkodu;
                                            $sayacgelen->serino = $serino;
                                            $sayacgelen->depotarihi = $gelistarih;
                                            $sayacgelen->sayacadi_id = $sayacadi;
                                            $sayacgelen->sayaccap_id = $sayaccap;
                                            $sayacgelen->uretimyer_id = $uretimyeri;
                                            $sayacgelen->servis_id = $depogelen->servis_id;
                                            $sayacgelen->subekodu = $subeyetkili->subekodu;
                                            $sayacgelen->kullanici_id = Auth::user()->id;
                                            $sayacgelen->beyanname = -2;
                                            $sayacgelen->sokulmenedeni = $sokulmenedeni;
                                            $sayacgelen->servissayaci = $servissayac;
                                            $sayacgelen->endeks = $endeks;
                                            $sayacgelen->save();
                                            $biten++;
                                            $adet++;
                                            $eklenenler .= ($eklenenler == "" ? "" : ",") . $serino;
                                            $secilenler .= ($secilenler == "" ? "" : ",") . $sayacgelen->id;
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
                                            $servistakip->subekodu = $subeyetkili->subekodu;
                                            $servistakip->depotarih = $sayacgelen->depotarihi;
                                            $servistakip->sayacgiristarihi = $sayacgelen->eklenmetarihi;
                                            $servistakip->kullanici_id = $sayacgelen->kullanici_id;
                                            $servistakip->sonislemtarihi = $sayacgelen->eklenmetarihi;
                                            $servistakip->save();
                                        } catch (Exception $e) {
                                            DB::rollBack();
                                            Log::error($e);
                                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayac Kayıdı Yapılamadı', 'text' => 'Servis Takip Kısmına Kayıt Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                        }
                                    }
                                }
                                if ($biten > 0) {
                                    try {
                                        $depolararasi = Depolararasi::where('servis_id', 6)->where('netsiscari_id', $netsiscari_id)->where('depokodu', $depogelen->depokodu)
                                            ->where('tipi', 0)->where('depodurum', 0)->first();
                                        if ($depolararasi) {
                                            $depolararasi->secilenler .= ($depolararasi->secilenler == "" ? "" : ",") . $secilenler; //bu bilgiler yok
                                            $depolararasi->sayacsayisi += $biten;
                                        } else {
                                            $depolararasi = new Depolararasi;
                                            $depolararasi->servis_id = 6;
                                            $depolararasi->netsiscari_id = $netsiscari_id;
                                            $depolararasi->secilenler = $secilenler;
                                            $depolararasi->sayacsayisi = $biten;
                                            $depolararasi->depodurum = 0;
                                            $depolararasi->depokodu = $depogelen->depokodu;
                                        }
                                        $depolararasi->save();
                                    } catch (Exception $e) {
                                        Log::error($e);
                                        DB::rollBack();
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayaç Kayıdı Yapılamadı', 'text' => 'Depolararası Kayıdı Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                    }
                                    try {
                                        BackendController::HatirlatmaGuncelle(2, $netsiscari_id, $servisid, $biten, $depogelen->id, $depogelen->servisstokkodu);
                                        BackendController::DepoDurumGuncelle($depogelen->id);
                                        BackendController::HatirlatmaEkle(3, $netsiscari_id, $servisid, $biten, $depogelen->id, $depogelen->servisstokkodu);
                                        BackendController::HatirlatmaEkle(11, $netsiscari_id, $servisid, $biten, $depogelen->id, $depogelen->servisstokkodu);
                                        BackendController::BildirimEkle(2, $netsiscari_id, $servisid, $biten, $depogelen->id, $depogelen->servisstokkodu);
                                    } catch (Exception $e) {
                                        DB::rollBack();
                                        Log::error($e);
                                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Sayac Kayıdı Yapılamadı', 'text' => 'Hatırlatma Kayıdı Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
                                    }
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
                DB::rollBack();
                Log::error($e);
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ambar Girişi Kaydedilemedi', 'text' => str_replace("'", "\'", $e->getMessage()), 'type' => 'error'));
            }
            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-plus-circle', $netsiscari->cariadi . ' Yerine Ait ' . $adet . ' Adet Sayacın Şube Sayaç Kayıdı Yapıldı.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Seri Numaraları:' . $eklenenler);
            DB::commit();
            return Redirect::to('sube/sayackayit')->with(array('mesaj' => 'true', 'title' => 'Sayaç Kayıdı Yapıldı', 'text' => 'Sayac Kayıdı Başarıyla Yapıldı', 'type' => 'success'));
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            Input::flash();
            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Arızalı Sayaç Kayıdı Yapılamadı', 'text' => str_replace("'", "\'", $e->getMessage()), 'type' => 'error'));
        }
    }

    public static function TruncateNumber( $number, $precision = 2)
    {
        // Zero causes issues, and no need to truncate
        if (0 == (int)$number) {
            return $number;
        }
        // Are we negative?
        $negative = $number / abs($number);
        // Cast the number to a positive to solve rounding
        $number = abs($number);
        // Calculate precision number for dividing / multiplying
        $precision = pow(10, $precision);
        // Run the math, re-applying the negative value to ensure returns correctly negative / positive
        return floor($number * $precision) / $precision * $negative;
    }

    public static function getNewId(){
        $id = DB::table('kullanici')->first(array(DB::raw('NEWID() AS GUID')));
        return $id->GUID;
    }

    public static function MuhasebeFisKaydet($sayacsatis,$status)
    {
        try {
            //$sayacsatis = SubeSayacSatis::find($id);
            //Log::info($sayacsatis);
            $abone = Abone::find($sayacsatis->abone_id);
            $netsiscari = NetsisCari::find($sayacsatis->netsiscari_id);
            $sube = Sube::where('subekodu',$sayacsatis->subekodu)->where('aktif',1)->first();
            $kasakod = KasaKod::where('subekodu',$sayacsatis->subekodu)->where('kasakod',$sayacsatis->kasakodu)->first();
            $guid = BackendController::getNewId();
            $muhfisno = BackendController::MuhFisNo($sayacsatis);
            if($muhfisno==0){
                return 0;
            }
            $sayacsatis->muhfisno = $muhfisno;
            $sayacsatis->save();
            // 1. Ürünün satış hesabı
            // 2. KDV hesabı
            // 3. Carinin borçlandırılması
            // 4. Carinin borcunun tahsili   açık faturada yok
            // 5. Paranın kasaya yansılıtması açık faturada yok
            $secilenler = explode(',', $sayacsatis->secilenler);
            $adetler = explode(',', $sayacsatis->adet);
            $birimfiyatlar = explode(',', $sayacsatis->birimfiyat);
            $ucretsizler = explode(',', $sayacsatis->ucretsiz);
            $urunler = $list = $muhkodlist = array();
            for ($i = 0; $i < count($secilenler); $i++) {
                $urun = SubeUrun::find($secilenler[$i]);
                $stokkod = NetsisStokKod::find($urun->netsisstokkod_id);
                $muhdetay = StMuhDetay::where('SUBE_KODU',$sayacsatis->subekodu)->where('MUH_DETAYKOD',$stokkod->muhkodu)->first();
                $satishesabi = $muhdetay ? $muhdetay->SATIS_HESABI : '';
                if(!$ucretsizler[$i]){
                    array_push($urunler, array('muhkodu'=>$stokkod->muhkodu,'adet'=>$adetler[$i],'fiyat'=>$birimfiyatlar[$i],'hesapkodu'=>$satishesabi));
                }
            }
            //Log::info($urunler);
            if(count($urunler)>0) {
                foreach ($urunler as $key => $row) {
                    $muhkodlist[$key] = $row['muhkodu'];
                }
                array_multisort($muhkodlist, SORT_ASC, $urunler);
                $i = 0;
                foreach ($urunler as $urun) {
                    $fiyat = $urun['fiyat'];
                    $birimkdv =  (($fiyat*18)/118);
                    $birimfiyat = $fiyat-$birimkdv;
                    $tutar = $birimfiyat*$urun['adet'];
                    if ($i == 0) {
                        array_push($list, array('muhkodu' => $urun['muhkodu'], 'urun' => array($urun), 'hesapkodu' => $urun['hesapkodu'],'tutar'=>$tutar));
                        $i++;
                    } else {
                        if ($list[$i - 1]['muhkodu'] == $urun['muhkodu']) {

                            $list[$i - 1]['tutar'] += $tutar;
                            array_push($list[$i - 1]['urun'], $urun);
                        } else {
                            array_push($list, array('muhkodu' => $urun['muhkodu'], 'urun' => array($urun), 'hesapkodu' => $urun['hesapkodu'],'tutar'=>$tutar));
                            $i++;
                        }
                    }
                }
            }
            $i = 1;
            foreach ($list as $kayit){ // listedeki her kalem için kayıt açılır
                // SIRA NO 1 Ürünün Satış Hesabı
                $muhfis = new MuhFis;
                $muhfis->AY_KODU = intval(date('n',strtotime($sayacsatis->faturatarihi)));
                $muhfis->FISNO = $muhfisno;
                $muhfis->SIRA = $i;
                $muhfis->HES_KOD = BackendController::ReverseTrk($kayit['hesapkodu']);
                $muhfis->TARIH = $sayacsatis->faturatarihi;
                $muhfis->BA = 2 ; //1 ALACAK 2 BORÇLU
                $muhfis->ACIKLAMA = BackendController::ReverseTrk(mb_substr(trim($netsiscari->cariadi),0,
                    (strlen(utf8_decode(trim($netsiscari->cariadi)))>10 ? 10 :strlen(utf8_decode(trim($netsiscari->cariadi)))),'UTF-8').'.'.
                    ($sube->netsiscari_id==$sayacsatis->netsiscari_id ? mb_substr(trim($abone->adisoyadi),0,
                        (strlen(utf8_decode(trim($abone->adisoyadi)))>20 ? 20 :strlen(utf8_decode(trim($abone->adisoyadi)))),'utf-8') : 'FT.MIZ').' NO:'.$sayacsatis->faturano);
                $muhfis->TUTAR = round($kayit['tutar'],2);
                $muhfis->REF_KOD = '';
                $muhfis->ACIKLAMA2 = '';
                $muhfis->DOVIZTIP = 0;
                $muhfis->FIRMADOVTIP = 0;
                $muhfis->UPDATEKODU = '';
                $muhfis->EVRAKTARIHI = $sayacsatis->faturatarihi;
                $muhfis->HESAPISMI = '';
                $muhfis->YONTEM = 'E';
                $muhfis->ENTEGREFKEY = '011' . $sayacsatis->faturano . BackendController::ReverseTrk($sayacsatis->carikod);
                $muhfis->YEDEK1 = '';
                $muhfis->YEDEK2 = '';
                $muhfis->YEDEK3 = 0;
                $muhfis->YEDEK4 = 0;
                $muhfis->YEDEK5 = 0;
                $muhfis->YEDEK6 = 0;
                $muhfis->YEDEK7 = 0;
                $muhfis->YEDEK8 = '';
                $muhfis->YEDEK9 = '';
                $muhfis->YEDEK10 = 0;
                $muhfis->YEDEK11 = date('Y-m-d',strtotime('1900-01-01'));
                $muhfis->PROJE_KODU = $sayacsatis->projekodu;
                $muhfis->SUBE_KODU = $sayacsatis->subekodu;
                $muhfis->SUBELI = '';
                $muhfis->HARKONTROL = 0;
                $muhfis->KAYITYAPANKUL = $sayacsatis->netsiskullanici;
                $muhfis->KAYITTARIHI = date('Y-m-d');
                $muhfis->DUZELTMEYAPANKUL = '';
                $muhfis->BFORMCARIKODU = '';
                $muhfis->BFORMBELGENO = '';
                $muhfis->GUID = $guid;
                $muhfis->ISLEMSIRANO = 2;
                $muhfis->KAYITSEKLI = 'D';
                $muhfis->MGECDK = $status ? 'CB' : 'KB'; //KB kapalı CB açık fatura
                $muhfis->KULL_ID = $sayacsatis->netsiskullanici_id;
                $muhfis->ISLEMTIPI = '';
                //Log::info($muhfis);
                $muhfis->save();

                $muhfisek = new MuhFisEk;
                $muhfisek->SUBE_KODU = $muhfis->SUBE_KODU;
                $muhfisek->MUHAY_KODU = $muhfis->AY_KODU;
                $muhfisek->MUHFISNO = $muhfis->FISNO;
                $muhfisek->MUHSIRA = $muhfis->SIRA;
                $sira = MuhFisEk::where('SUBE_KODU',$muhfis->SUBE_KODU)->where('MUHAY_KODU',$muhfis->AY_KODU)->orderBy('INCKEY_NO','desc')->first();
                $muhfisek->AYSIRA = ($sira ? $sira->AYSIRA : 0)+1;
                $muhfisek->BELGE_TIPI = 'Fatura';
                $muhfisek->BELGE_NO = $sayacsatis->faturano;
                $muhfisek->TARIH = $sayacsatis->faturatarihi;
                $muhfisek->ISLEM_TIPI = 1;
                $muhfisek->save();
                $i++;
            }
            // SIRA NO 2 KDV Hesabı
            $muhfis = new MuhFis;
            $muhfis->AY_KODU = intval(date('n',strtotime($sayacsatis->faturatarihi)));
            $muhfis->FISNO = $muhfisno;
            $muhfis->SIRA = $i;
            $muhfis->HES_KOD = '391-01-018'; //KDV %18 HESAP KODU
            $muhfis->TARIH = $sayacsatis->faturatarihi;
            $muhfis->BA = 2 ; //1 BORÇ 2 ALACAK
            $length = strlen(utf8_decode(mb_substr(trim($netsiscari->cariadi),0,
                (strlen(utf8_decode(trim($netsiscari->cariadi)))>10 ? 10 :strlen(utf8_decode(trim($netsiscari->cariadi)))),'utf-8').'.'.
                ($sube->netsiscari_id==$sayacsatis->netsiscari_id ? mb_substr(trim($abone->adisoyadi),0,
                    (strlen(utf8_decode(trim($abone->adisoyadi)))>20 ? 20 :strlen(utf8_decode(trim($abone->adisoyadi)))),'utf-8') : 'FT.MIZ').' NO:'.$sayacsatis->faturano.' KDVSI'));
            $muhfis->ACIKLAMA = BackendController::ReverseTrk(mb_substr(mb_substr(trim($netsiscari->cariadi),0,
                (strlen(utf8_decode(trim($netsiscari->cariadi)))>10 ? 10 :strlen(utf8_decode(trim($netsiscari->cariadi)))),'utf-8').'.'.
                ($sube->netsiscari_id==$sayacsatis->netsiscari_id ? mb_substr(trim($abone->adisoyadi),0,
                    (strlen(utf8_decode(trim($abone->adisoyadi)))>20 ? 20 :strlen(utf8_decode(trim($abone->adisoyadi)))),'utf-8') : 'FT.MIZ').' NO:'.$sayacsatis->faturano.' KDVSI',0,
                ($length>50 ? 50 : $length),'utf-8'));
            $muhfis->TUTAR = $sayacsatis->kdv;
            $muhfis->REF_KOD = '';
            $muhfis->ACIKLAMA2 = '';
            $muhfis->DOVIZTIP = 0;
            $muhfis->FIRMADOVTIP = 0;
            $muhfis->UPDATEKODU = '';
            $muhfis->EVRAKTARIHI = $sayacsatis->faturatarihi;
            $muhfis->HESAPISMI = '';
            $muhfis->YONTEM = 'E';
            $muhfis->ENTEGREFKEY = '011' . $sayacsatis->faturano . BackendController::ReverseTrk($sayacsatis->carikod);
            $muhfis->YEDEK1 = '';
            $muhfis->YEDEK2 = '';
            $muhfis->YEDEK3 = 0;
            $muhfis->YEDEK4 = 0;
            $muhfis->YEDEK5 = 0;
            $muhfis->YEDEK6 = 0;
            $muhfis->YEDEK7 = 0;
            $muhfis->YEDEK8 = '';
            $muhfis->YEDEK9 = '';
            $muhfis->YEDEK10 = 0;
            $muhfis->YEDEK11 = date('Y-m-d',strtotime('1900-01-01'));
            $muhfis->PROJE_KODU = $sayacsatis->projekodu;
            $muhfis->SUBE_KODU = $sayacsatis->subekodu;
            $muhfis->SUBELI = '';
            $muhfis->HARKONTROL = 0;
            $muhfis->KAYITYAPANKUL = $sayacsatis->netsiskullanici;
            $muhfis->KAYITTARIHI = date('Y-m-d');
            $muhfis->DUZELTMEYAPANKUL = '';
            $muhfis->BFORMCARIKODU = '';
            $muhfis->BFORMBELGENO = '';
            $muhfis->GUID = $guid;
            $muhfis->ISLEMSIRANO = 2;
            $muhfis->KAYITSEKLI = 'D';
            $muhfis->MGECDK = $status ? 'CB' : 'KB'; //KB kapalı CB açık fatura
            $muhfis->KULL_ID = $sayacsatis->netsiskullanici_id;
            $muhfis->ISLEMTIPI = '';
            $muhfis->save();

            $muhfisek = new MuhFisEk;
            $muhfisek->SUBE_KODU = $muhfis->SUBE_KODU;
            $muhfisek->MUHAY_KODU = $muhfis->AY_KODU;
            $muhfisek->MUHFISNO = $muhfis->FISNO;
            $muhfisek->MUHSIRA = $muhfis->SIRA;
            $sira = MuhFisEk::where('SUBE_KODU',$muhfis->SUBE_KODU)->where('MUHAY_KODU',$muhfis->AY_KODU)->orderBy('INCKEY_NO','desc')->first();
            $muhfisek->AYSIRA = ($sira ? $sira->AYSIRA : 0)+1;
            $muhfisek->BELGE_TIPI = 'Fatura';
            $muhfisek->BELGE_NO = $sayacsatis->faturano;
            $muhfisek->TARIH = $sayacsatis->faturatarihi;
            $muhfisek->ISLEM_TIPI = 1;
            $muhfisek->save();
            $i++;

            // SIRA NO 3 Carinin Borçlanması
            $muhfis = new MuhFis;
            $muhfis->AY_KODU = intval(date('n',strtotime($sayacsatis->faturatarihi)));
            $muhfis->FISNO = $muhfisno;
            $muhfis->SIRA = $i;
            $muhfis->HES_KOD = BackendController::ReverseTrk($netsiscari->carikod);
            $muhfis->TARIH = $sayacsatis->faturatarihi;
            $muhfis->BA = $status ? 1 : 2 ; //1 BORÇ 2 ALACAK
            $muhfis->ACIKLAMA = BackendController::ReverseTrk(mb_substr(trim($netsiscari->cariadi),0,
                (strlen(utf8_decode(trim($netsiscari->cariadi)))>10 ? 10 :strlen(utf8_decode(trim($netsiscari->cariadi)))),'utf-8').'.'.
                ($sube->netsiscari_id==$sayacsatis->netsiscari_id ? mb_substr(trim($abone->adisoyadi),0,
                    (strlen(utf8_decode(trim($abone->adisoyadi)))>20 ? 20 :strlen(utf8_decode(trim($abone->adisoyadi)))),'utf-8') : 'FT.MIZ').' NO:'.$sayacsatis->faturano);
            $muhfis->TUTAR = $sayacsatis->toplamtutar;
            $muhfis->REF_KOD = '';
            $muhfis->ACIKLAMA2 = '';
            $muhfis->DOVIZTIP = 0;
            $muhfis->FIRMADOVTIP = 0;
            $muhfis->UPDATEKODU = '';
            $muhfis->EVRAKTARIHI = $sayacsatis->faturatarihi;
            $muhfis->HESAPISMI = '';
            $muhfis->YONTEM = 'E';
            $muhfis->ENTEGREFKEY = '011' . $sayacsatis->faturano . BackendController::ReverseTrk($sayacsatis->carikod);
            $muhfis->YEDEK1 = '';
            $muhfis->YEDEK2 = '';
            $muhfis->YEDEK3 = 0;
            $muhfis->YEDEK4 = 0;
            $muhfis->YEDEK5 = 0;
            $muhfis->YEDEK6 = 0;
            $muhfis->YEDEK7 = 0;
            $muhfis->YEDEK8 = '';
            $muhfis->YEDEK9 = '';
            $muhfis->YEDEK10 = 0;
            $muhfis->YEDEK11 = date('Y-m-d',strtotime('1900-01-01'));
            $muhfis->PROJE_KODU = $sayacsatis->projekodu;
            $muhfis->SUBE_KODU = $sayacsatis->subekodu;
            $muhfis->SUBELI = '';
            $muhfis->HARKONTROL = 0;
            $muhfis->KAYITYAPANKUL = $sayacsatis->netsiskullanici;
            $muhfis->KAYITTARIHI = date('Y-m-d');
            $muhfis->DUZELTMEYAPANKUL = '';
            $muhfis->BFORMCARIKODU = '';
            $muhfis->BFORMBELGENO = '';
            $muhfis->GUID = $guid;
            $muhfis->ISLEMSIRANO = 2;
            $muhfis->KAYITSEKLI = 'D';
            $muhfis->MGECDK = $status ? 'CB' : 'KB'; //KB kapalı CB açık fatura
            $muhfis->KULL_ID = $sayacsatis->netsiskullanici_id;
            $muhfis->ISLEMTIPI = '';
            $muhfis->save();

            $muhfisek = new MuhFisEk;
            $muhfisek->SUBE_KODU = $muhfis->SUBE_KODU;
            $muhfisek->MUHAY_KODU = $muhfis->AY_KODU;
            $muhfisek->MUHFISNO = $muhfis->FISNO;
            $muhfisek->MUHSIRA = $muhfis->SIRA;
            $sira = MuhFisEk::where('SUBE_KODU',$muhfis->SUBE_KODU)->where('MUHAY_KODU',$muhfis->AY_KODU)->orderBy('INCKEY_NO','desc')->first();
            $muhfisek->AYSIRA = ($sira ? $sira->AYSIRA : 0)+1;
            $muhfisek->BELGE_TIPI = 'Fatura';
            $muhfisek->BELGE_NO = $sayacsatis->faturano;
            $muhfisek->TARIH = $sayacsatis->faturatarihi;
            $muhfisek->ISLEM_TIPI = 1;
            $muhfisek->save();
            $i++;

            if(!$status){
                // SIRA NO 4 Carinin borcunun tahsili
                $muhfis = new MuhFis;
                $muhfis->AY_KODU = intval(date('n',strtotime($sayacsatis->faturatarihi)));
                $muhfis->FISNO = $muhfisno;
                $muhfis->SIRA = $i;
                $muhfis->HES_KOD = BackendController::ReverseTrk($netsiscari->carikod);
                $muhfis->TARIH = $sayacsatis->faturatarihi;
                $muhfis->BA = 1 ; //1 BORÇ 2 ALACAK
                $muhfis->ACIKLAMA = BackendController::ReverseTrk(mb_substr(trim($netsiscari->cariadi),0,
                    (strlen(utf8_decode(trim($netsiscari->cariadi)))>10 ? 10 :strlen(utf8_decode(trim($netsiscari->cariadi)))),'utf-8').'.'.
                    ($sube->netsiscari_id==$sayacsatis->netsiscari_id ? mb_substr(trim($abone->adisoyadi),0,
                        (strlen(utf8_decode(trim($abone->adisoyadi)))>20 ? 20 :strlen(utf8_decode(trim($abone->adisoyadi)))),'utf-8') : 'FT.MIZ').' NO:'.$sayacsatis->faturano);
                $muhfis->TUTAR = $sayacsatis->toplamtutar;
                $muhfis->REF_KOD = '';
                $muhfis->ACIKLAMA2 = '';
                $muhfis->DOVIZTIP = 0;
                $muhfis->FIRMADOVTIP = 0;
                $muhfis->UPDATEKODU = '';
                $muhfis->EVRAKTARIHI = $sayacsatis->faturatarihi;
                $muhfis->HESAPISMI = '';
                $muhfis->YONTEM = 'E';
                $muhfis->ENTEGREFKEY = '011' . $sayacsatis->faturano . BackendController::ReverseTrk($sayacsatis->carikod);
                $muhfis->YEDEK1 = '';
                $muhfis->YEDEK2 = '';
                $muhfis->YEDEK3 = 0;
                $muhfis->YEDEK4 = 0;
                $muhfis->YEDEK5 = 0;
                $muhfis->YEDEK6 = 0;
                $muhfis->YEDEK7 = 0;
                $muhfis->YEDEK8 = '';
                $muhfis->YEDEK9 = '';
                $muhfis->YEDEK10 = 0;
                $muhfis->YEDEK11 = date('Y-m-d',strtotime('1900-01-01'));
                $muhfis->PROJE_KODU = $sayacsatis->projekodu;
                $muhfis->SUBE_KODU = $sayacsatis->subekodu;
                $muhfis->SUBELI = '';
                $muhfis->HARKONTROL = 0;
                $muhfis->KAYITYAPANKUL = $sayacsatis->netsiskullanici;
                $muhfis->KAYITTARIHI = date('Y-m-d');
                $muhfis->DUZELTMEYAPANKUL = '';
                $muhfis->BFORMCARIKODU = '';
                $muhfis->BFORMBELGENO = '';
                $muhfis->GUID = $guid;
                $muhfis->ISLEMSIRANO = 2;
                $muhfis->KAYITSEKLI = 'D';
                $muhfis->MGECDK = $status ? 'CB' : 'KB'; //KB kapalı CB açık fatura
                $muhfis->KULL_ID = $sayacsatis->netsiskullanici_id;
                $muhfis->ISLEMTIPI = '';
                $muhfis->save();

                $muhfisek = new MuhFisEk;
                $muhfisek->SUBE_KODU = $muhfis->SUBE_KODU;
                $muhfisek->MUHAY_KODU = $muhfis->AY_KODU;
                $muhfisek->MUHFISNO = $muhfis->FISNO;
                $muhfisek->MUHSIRA = $muhfis->SIRA;
                $sira = MuhFisEk::where('SUBE_KODU',$muhfis->SUBE_KODU)->where('MUHAY_KODU',$muhfis->AY_KODU)->orderBy('INCKEY_NO','desc')->first();
                $muhfisek->AYSIRA = ($sira ? $sira->AYSIRA : 0)+1;
                $muhfisek->BELGE_TIPI = 'Fatura';
                $muhfisek->BELGE_NO = $sayacsatis->faturano;
                $muhfisek->TARIH = $sayacsatis->faturatarihi;
                $muhfisek->ISLEM_TIPI = 1;
                $muhfisek->save();
                $i++;
                // SIRA NO 5 Paranın kasaya yansılıtması
                $muhfis = new MuhFis;
                $muhfis->AY_KODU = intval(date('n',strtotime($sayacsatis->faturatarihi)));
                $muhfis->FISNO = $muhfisno;
                $muhfis->SIRA = $i;
                $muhfis->HES_KOD = BackendController::ReverseTrk($kasakod->muhasebekodu);
                $muhfis->TARIH = $sayacsatis->faturatarihi;
                $muhfis->BA = 1 ; //1 BORÇ 2 ALACAK
                $muhfis->ACIKLAMA = BackendController::ReverseTrk(mb_substr(trim($netsiscari->cariadi),0,
                    (strlen(utf8_decode(trim($netsiscari->cariadi)))>10 ? 10 :strlen(utf8_decode(trim($netsiscari->cariadi)))),'utf-8').'.'.
                    ($sube->netsiscari_id==$sayacsatis->netsiscari_id ? mb_substr(trim($abone->adisoyadi),0,
                        (strlen(utf8_decode(trim($abone->adisoyadi)))>20 ? 20 :strlen(utf8_decode(trim($abone->adisoyadi)))),'utf-8') : 'FT.MIZ').' NO:'.$sayacsatis->faturano);
                $muhfis->TUTAR = $sayacsatis->toplamtutar;
                $muhfis->REF_KOD = '';
                $muhfis->ACIKLAMA2 = '';
                $muhfis->DOVIZTIP = 0;
                $muhfis->FIRMADOVTIP = 0;
                $muhfis->UPDATEKODU = '';
                $muhfis->EVRAKTARIHI = $sayacsatis->faturatarihi;
                $muhfis->HESAPISMI = '';
                $muhfis->YONTEM = '';
                $muhfis->ENTEGREFKEY = '011' . $sayacsatis->faturano . BackendController::ReverseTrk($sayacsatis->carikod);
                $muhfis->YEDEK1 = '';
                $muhfis->YEDEK2 = '';
                $muhfis->YEDEK3 = 0;
                $muhfis->YEDEK4 = 0;
                $muhfis->YEDEK5 = 0;
                $muhfis->YEDEK6 = 0;
                $muhfis->YEDEK7 = 0;
                $muhfis->YEDEK8 = '';
                $muhfis->YEDEK9 = '';
                $muhfis->YEDEK10 = 0;
                $muhfis->YEDEK11 = date('Y-m-d',strtotime('1900-01-01'));
                $muhfis->PROJE_KODU = $sayacsatis->projekodu;
                $muhfis->SUBE_KODU = $sayacsatis->subekodu;
                $muhfis->SUBELI = '';
                $muhfis->HARKONTROL = 0;
                $muhfis->KAYITYAPANKUL = $sayacsatis->netsiskullanici;
                $muhfis->KAYITTARIHI = date('Y-m-d');
                $muhfis->DUZELTMEYAPANKUL = '';
                $muhfis->BFORMCARIKODU = '';
                $muhfis->BFORMBELGENO = '';
                $muhfis->GUID = $guid;
                $muhfis->ISLEMSIRANO = 2;
                $muhfis->KAYITSEKLI = 'D';
                $muhfis->MGECDK = $status ? 'CB' : 'KB'; //KB kapalı CB açık fatura
                $muhfis->KULL_ID = $sayacsatis->netsiskullanici_id;
                $muhfis->ISLEMTIPI = '';
                $muhfis->save();

                $muhfisek = new MuhFisEk;
                $muhfisek->SUBE_KODU = $muhfis->SUBE_KODU;
                $muhfisek->MUHAY_KODU = $muhfis->AY_KODU;
                $muhfisek->MUHFISNO = $muhfis->FISNO;
                $muhfisek->MUHSIRA = $muhfis->SIRA;
                $sira = MuhFisEk::where('SUBE_KODU',$muhfis->SUBE_KODU)->where('MUHAY_KODU',$muhfis->AY_KODU)->orderBy('INCKEY_NO','desc')->first();
                $muhfisek->AYSIRA = ($sira ? $sira->AYSIRA : 0)+1;
                $muhfisek->BELGE_TIPI = 'Fatura';
                $muhfisek->BELGE_NO = $sayacsatis->faturano;
                $muhfisek->TARIH = $sayacsatis->faturatarihi;
                $muhfisek->ISLEM_TIPI = 1;
                $muhfisek->save();
            }
            return 1;
        } catch (Exception $e) {
            Log::error($e);
            return 0;
        }
    }

    public static function MuhasebeFisSil($sayacsatis)
    {
        try {
            $muhfisno = $sayacsatis->muhfisno;
            $muhfis = MuhFis::where('AY_KODU', intval(date('n', strtotime($sayacsatis->faturatarihi))))->where('FISNO', $muhfisno)
                ->where('SUBE_KODU', $sayacsatis->subekodu)->get();
            foreach ($muhfis as $muhfis_) {
                $muhfis_->delete();
            }
            $muhfisek = MuhFisEk::where('MUHAY_KODU', intval(date('n', strtotime($sayacsatis->faturatarihi))))->where('MUHFISNO', $muhfisno)
                ->where('SUBE_KODU', $sayacsatis->subekodu)->get();
            foreach ($muhfisek as $muhfisek_) {
                $muhfisek_->delete();
            }
            $muhmas = MuhMas::where('AY_KODU', intval(date('n', strtotime($sayacsatis->faturatarihi))))->where('MAS_FISNO', $muhfisno)
                ->where('SUBE_KODU', $sayacsatis->subekodu)->where('FISTIP','T')->first();
            if(!$muhmas)
                return 0;
            $muhmas->delete();
            return 1;
        } catch (Exception $e) {
            Log::error($e);
            return 0;
        }
    }

    public static function CariHareketKaydet($sayacsatis,$status)
    {
        try {
            //$sayacsatis = SubeSayacSatis::find($id);
            $abone = Abone::find($sayacsatis->abone_id);
            $netsiscari = NetsisCari::find($sayacsatis->netsiscari_id);
            $sube = Sube::where('subekodu',$sayacsatis->subekodu)->where('aktif',1)->first();

            // 1. Borç
            // 2. Alacak açık faturada yok
            $cahar = new Cahar;
            $cahar->SUBE_KODU = $sayacsatis->subekodu;
            $cahar->CARI_KOD = BackendController::ReverseTrk($netsiscari->carikod);
            $cahar->TARIH = $sayacsatis->faturatarihi;
            $cahar->VADE_TARIHI = date($sayacsatis->faturatarihi, strtotime($sayacsatis->faturatarihi . ' + ' . $netsiscari->vadegunu . ' days'));
            $cahar->BELGE_NO = $sayacsatis->faturano;
            $cahar->ACIKLAMA = ($sube->netsiscari_id==$sayacsatis->netsiscari_id ? mb_substr(trim($abone->adisoyadi),0,
                (strlen(utf8_decode(trim($abone->adisoyadi)))>20 ? 20 :strlen(utf8_decode(trim($abone->adisoyadi)))),'utf-8') : 'FATURAMIZ').' (0)';
            $cahar->HKA = 'A';
            $cahar->BORC = $sayacsatis->toplamtutar;
            $cahar->ALACAK = 0.00;
            $cahar->RAPOR_KODU = ' ';
            $cahar->F9SC = ' ';
            $cahar->HAREKET_TURU = 'B';
            $cahar->ODEME_GUNU = $netsiscari->vadegunu;
            $cahar->PLASIYER_KODU = $sayacsatis->plasiyerkod;
            $cahar->ENT_REF_KEY = '011' . $sayacsatis->faturano . BackendController::ReverseTrk($sayacsatis->carikod);
            $cahar->RAPOR_KODU2 = '';
            $cahar->DUZELTMETARIHI = date('Y-m-d H:i:s');
            $cahar->PROJE_KODU = $sayacsatis->projekodu;
            $cahar->save();

            if(!$status){
                $cahar = new Cahar;
                $cahar->SUBE_KODU = $sayacsatis->subekodu;
                $cahar->CARI_KOD = BackendController::ReverseTrk($netsiscari->carikod);
                $cahar->TARIH = $sayacsatis->faturatarihi;
                $cahar->VADE_TARIHI = date($sayacsatis->faturatarihi, strtotime($sayacsatis->faturatarihi . ' + ' . $netsiscari->vadegunu . ' days'));
                $cahar->BELGE_NO = $sayacsatis->faturano;
                $cahar->ACIKLAMA = ($sube->netsiscari_id==$sayacsatis->netsiscari_id ? mb_substr(trim($abone->adisoyadi),0,
                        (strlen(utf8_decode(trim($abone->adisoyadi)))>20 ? 20 :strlen(utf8_decode(trim($abone->adisoyadi)))),'utf-8') : 'FATURAMIZ').' (0)';
                $cahar->HKA = 'A';
                $cahar->BORC = 0.00;
                $cahar->ALACAK = $sayacsatis->toplamtutar;
                $cahar->RAPOR_KODU = ' ';
                $cahar->F9SC = ' ';
                $cahar->HAREKET_TURU = 'D';
                $cahar->ODEME_GUNU = $netsiscari->vadegunu;
                $cahar->PLASIYER_KODU = $sayacsatis->plasiyerkod;
                $cahar->ENT_REF_KEY = '011' . $sayacsatis->faturano . BackendController::ReverseTrk($sayacsatis->carikod);
                $cahar->RAPOR_KODU2 = '';
                $cahar->DUZELTMETARIHI = date('Y-m-d H:i:s');
                $cahar->PROJE_KODU = $sayacsatis->projekodu;
                $cahar->save();
            }
            return 1;
        } catch (Exception $e) {
            Log::error($e);
            return 0;
        }
    }

    public static function CariHareketSil($sayacsatis)
    {
        try {
            $netsiscari = NetsisCari::find($sayacsatis->netsiscari_id);
            $cahar = Cahar::where('SUBE_KODU',$sayacsatis->subekodu)->where('CARI_KOD',BackendController::ReverseTrk($netsiscari->carikod))
            ->where('BELGE_NO',$sayacsatis->faturano)->get();
            if($cahar->count()>0){
                foreach ($cahar as $char){
                    $char->delete();
                }
            }else{
                return 0;
            }
            return 1;
        } catch (Exception $e) {
            Log::error($e);
            return 0;
        }
    }

    public static function BasimLogEkle($id){
        try {
            $sayacsatis = SubeSayacSatis::find($id);
            $basimlog = new BasimLog;
            $basimlog->SUBE_KODU = $sayacsatis->subekodu;
            $basimlog->TARIH = $sayacsatis->faturatarihi;
            $basimlog->MODULNO = 1;
            $basimlog->PROGRAMNO = 1;
            $basimlog->KULLANICINO = 29;
            $basimlog->ISTEMCIADI = 'NETSIS';
            $basimlog->YAZICIADI = 'EPSON LX-350 ESC/P';
            $basimlog->UYGULAMAADI = 'DIZAYN.DLL';
            $basimlog->DIZAYNTIPI = 1;
            $basimlog->DIZAYNNO = 121;
            $basimlog->ANAHTAR = '<YERKODU>' . $sayacsatis->subekodu . '</YERKODU><TIP>1</TIP><NO>' . $sayacsatis->faturano . '</NO><CARI>' . $sayacsatis->carikod . '</CARI>';
            $basimlog->KAYITYAPANKUL = $sayacsatis->netsiskullanici;
            $basimlog->KAYITTARIHI = date('Y-m-d H:i:s');
            $basimlog->HATA = '';
            $basimlog->save();
        } catch (Exception $e) {
            Log::error($e);
        }
    }

    public static function MuhFisNo($sayacsatis){
        try{
            $fisno = BackendController::StringZeroAdd("".$sayacsatis->subekodu,5);
            $muhmas = MuhMas::where('AY_KODU',intval(date("n", strtotime($sayacsatis->faturatarihi))))->where('MAS_FISNO','LIKE',$fisno.'%')->where('SUBE_KODU',$sayacsatis->subekodu)->orderBy('MAS_FISNO','desc')->first();
            if($muhmas)
                $muhfisno = BackendController::FisNo($muhmas->MAS_FISNO,$fisno,1);
            else
                $muhfisno = $fisno.'0000000001';
            $muhmas = new MuhMas;
            $muhmas->AY_KODU = intval(date("n", strtotime($sayacsatis->faturatarihi)));
            $muhmas->MAS_FISNO = $muhfisno;
            $muhmas->MASACIK1 = BackendController::ReverseTrk('KASA TAHSİLATLARI');
            $muhmas->RES_YEVM_NO = 0;
            $muhmas->FISTIP = 'T'; //Tahsil
            $muhmas->VERSIYON = '5.0@0';
            $muhmas->YEDEK8 = 0;
            $muhmas->SUBE_KODU = $sayacsatis->subekodu;
            $muhmas->KAYITYAPANKUL = $sayacsatis->netsiskullanici;
            $muhmas->KAYITTARIHI = date('Y-m-d');
            $muhmas->MASACIK3 = 'KULL:'.$sayacsatis->netsiskullanici_id;
            $muhmas->save();
            return $muhfisno;
        } catch (Exception $e) {
            Log::error($e);
            return 0;
        }
    }

    public static function getCarikodsorgula(){
        $carikod = Input::get('carikodu');
        if($carikod){
            $carisub=BackendController::ReverseTrk(mb_substr($carikod,0,8,'utf-8'));
            $casabit =Casabit::where('CARI_KOD','LIKE',$carisub.'%')->orderBy('CARI_KOD','desc')->first();
            if($casabit){
                $carisubek = mb_substr(BackendController::Trk($casabit->CARI_KOD),strlen(utf8_decode($carisub)),11-strlen(utf8_decode($carisub)),'utf-8');
                if(isset($carisubek) && strlen($carisubek)>0){
                    $digitlength=11-strlen(utf8_decode($carisub));
                    $pattern="%0".$digitlength."d";
                    $carikod =  $carisub.preg_replace_callback( '/(\\d+)/', function($match) use($pattern) { return (sprintf($pattern,($match[0]+1))); }, $carisubek );
                }
            }else{
                $carikod = $carisub.'001';
            }
            return array("durum" => true,"carikod"=>BackendController::Trk($carikod));
        }else{
            return array("durum" => false,"carikod"=>$carikod);
        }
    }

    public static function StokHareketGrupla($secilenler,$miktarlar,$depokodlari, $stokkodlari,$muhkodlari){
        $list=$urunler=$secilenlist=$miktarlist=$depokodlist=$stokkodlist=$muhkodlist=array();
        $secilenler = explode(',',$secilenler);
        $miktarlar = explode(',',$miktarlar);
        $depokodlari = explode(',',$depokodlari);
        $stokkodlari = explode(',',$stokkodlari);
        $muhkodlari = explode(',',$muhkodlari);
        for($i=0;$i<count($secilenler);$i++)
        {
            $urun=$secilenler[$i];
            $miktar=$miktarlar[$i];
            $depokodu=$depokodlari[$i];
            $stokkodu=$stokkodlari[$i];
            $muhkodu=$muhkodlari[$i];
            if($urun!="")
            {
                array_push($urunler,array('urun'=>$urun,'miktar'=>$miktar,'depokodu'=>$depokodu,'stokkodu'=>$stokkodu,'muhkodu'=>$muhkodu));
            }
        }

        if(count($urunler)>0) {
            foreach ($urunler as $key => $row) {
                $secilenlist[$key] = $row['urun'];
                $miktarlist[$key] = $row['miktar'];
                $depokodlist[$key] = $row['depokodu'];
                $stokkodlist[$key] = $row['stokkodu'];
                $muhkodlist[$key] = $row['muhkodu'];
            }

            array_multisort($secilenler, SORT_ASC, $urunler);
            $i = 0;
            foreach ($urunler as $urun) {
                if ($i == 0) {
                    array_push($list, array('urunid' => $urun['urun'], 'urun' => array($urun), 'miktar' => $urun['miktar']));
                    $i++;
                } else {
                    if ($list[$i - 1]['urunid'] == $urun['urun']) {
                        $list[$i - 1]['miktar'] += $urun['miktar'];
                        array_push($list[$i - 1]['urun'], $urun);
                    } else {
                        array_push($list, array('urunid' => $urun['urun'], 'urun' => array($urun), 'miktar' => $urun['miktar']));
                        $i++;
                    }
                }
            }
        }
        return $list;
    }

    public static function NetsisSubeStokHareket($stokgiriscikis,$kalemler)
    {
        $subekodu = $stokgiriscikis->subekodu;
        $servisyetkili = ServisYetkili::where('kullanici_id', Auth::user()->id)->first();
        $kalemadedi = count($kalemler);
        $kasakod = KasaKod::where('subekodu', $subekodu)->orderBy('kasaadi', 'asc')->first();
        if (!$kasakod) {
            return array('durum' => '0', 'text' => 'Şube için Kasa Kodları Bulunamadı!');
        }
        $kasakodu = $kasakod->kasakod;
        if (!$servisyetkili) {
            return array('durum' => '0', 'text' => 'Bu Şube İçin Stok Hareketi Ekleme Yetkiniz Yok');
        }
        $fisno = $stokgiriscikis->fisno;
        $inckey = array();
        $inckeyler = "";
        try {
            if ($stokgiriscikis->gckod == "G") { // ambar girişi
                $fatura = Fatuirs::where('SUBE_KODU', $subekodu)->where('FTIRSIP', '9')->where('FATIRS_NO', BackendController::ReverseTrk($fisno))
                    ->where('CARI_KODU', BackendController::ReverseTrk($stokgiriscikis->masrafkodu))->first();
                if ($fatura)
                    return array('durum' => '0', 'text' => 'Bu Fatura Numarası Bu Cari için Sistemde Kayıtlı');

                $fatura = new Fatuirs;
                $fatura->SUBE_KODU = $subekodu;
                $fatura->FATIRS_NO = BackendController::ReverseTrk($fisno);  //ornek A00000000633911
                $fatura->FTIRSIP = '9';    // 9 AMBAR GİRİŞİ
                $fatura->CARI_KODU = $stokgiriscikis->masrafkodu;
                $fatura->TARIH = $stokgiriscikis->tarih;
                $fatura->TIPI = 0;
                $fatura->BRUTTUTAR = 0;
                $fatura->KDV = 0;
                $fatura->GENELTOPLAM = 0; //genel toplam
                $fatura->ACIKLAMA = NULL;
                $fatura->KOD1 = 0;
                $fatura->ODEMETARIHI = $stokgiriscikis->tarih;
                $fatura->KDV_DAHILMI = 'E';
                $fatura->FATKALEM_ADEDI = $kalemadedi; //kalem adedi
                $fatura->SIPARIS_TEST = $stokgiriscikis->tarih;
                $fatura->YEDEK22 = $stokgiriscikis->harekettur;
                $fatura->YEDEK = 'X';
                $fatura->PLA_KODU = NULL;
                $fatura->DOVIZTIP = 0;
                $fatura->DOVIZTUT = 0;
                $fatura->KS_KODU = $kasakodu;
                $fatura->F_YEDEK4 = 0;
                $fatura->C_YEDEK6 = 'M';
                $fatura->D_YEDEK10 = $stokgiriscikis->tarih;
                $fatura->PROJE_KODU = $stokgiriscikis->projekodu;
                $fatura->KAYITYAPANKUL = $servisyetkili->netsiskullanici;
                $fatura->KAYITTARIHI = date('Y-m-d H:i:s');
                $fatura->GELSUBE_KODU = 0;
                $fatura->GITSUBE_KODU = 0;
                $fatura->ISLETME_KODU = 1;
                $fatura->save();
                try {
                    $faturaek = new Fatuek;
                    $faturaek->SUBE_KODU = $subekodu;
                    $faturaek->FKOD = '9';
                    $faturaek->FATIRSNO = BackendController::ReverseTrk($fisno);
                    $faturaek->CKOD = BackendController::ReverseTrk($stokgiriscikis->masrafkodu);
                    $faturaek->ACIK1 = BackendController::ReverseTrk($stokgiriscikis->aciklama);
                    $faturaek->ACIK2 = BackendController::ReverseTrk($stokgiriscikis->aciklama2);
                    $faturaek->ACIK3 = BackendController::ReverseTrk($stokgiriscikis->aciklama3);
                    $faturaek->save();
                    $i = 1;
                    try {
                        foreach ($kalemler as $kalem) {
                            $faturakalem = new Sthar;
                            $urun = SubeUrun::find($kalem['urunid']);
                            if (!$urun) {
                                $fatura->delete();
                                $faturaek->delete();
                                Sthar::where(['SUBE_KODU' => $subekodu, 'FISNO' => BackendController::ReverseTrk($fisno), 'STHAR_FTIRSIP' => '9',
                                    'CARI_KODU' => $stokgiriscikis->masrafkodu])->delete();
                                return array('durum' => '0', 'text' => 'Stok Hareketine Ait Kalem Bilgisi Bulunamadı.');
                            }
                            $faturakalem->STOK_KODU = $kalem['urun'][0]['stokkodu'];
                            $faturakalem->FISNO = BackendController::ReverseTrk($fisno);
                            $faturakalem->STHAR_GCMIK = $kalem['miktar'];
                            $faturakalem->STHAR_GCKOD = 'G';
                            $faturakalem->STHAR_TARIH = $stokgiriscikis->tarih;
                            $faturakalem->STHAR_NF = 0;
                            $faturakalem->STHAR_BF = 0;
                            $faturakalem->STHAR_KDV = 0;
                            $faturakalem->STHAR_DOVTIP = 0;
                            $faturakalem->STHAR_DOVFIAT = 0;
                            $faturakalem->DEPO_KODU = $kalem['urun'][0]['depokodu'];
                            $faturakalem->STHAR_ACIKLAMA = BackendController::ReverseTrk($stokgiriscikis->masrafkodu);
                            $faturakalem->STHAR_FTIRSIP = '9';
                            $faturakalem->LISTE_FIAT = 0;
                            $faturakalem->STHAR_HTUR = $stokgiriscikis->harekettur;
                            $faturakalem->STHAR_ODEGUN = 0;
                            $faturakalem->STHAR_BGTIP = 'I';
                            $faturakalem->STHAR_KOD1 = NULL;
                            $faturakalem->STHAR_KOD2 = 'M';
                            $faturakalem->STHAR_CARIKOD = BackendController::ReverseTrk($stokgiriscikis->masrafkodu);
                            $faturakalem->STHAR_SIP_TURU = 'M';
                            $faturakalem->PLASIYER_KODU = NULL;
                            $faturakalem->EKALAN = BackendController::ReverseTrk($stokgiriscikis->masrafkodu);;
                            $faturakalem->SIRA = $i;
                            $faturakalem->STRA_SIPKONT = 0;
                            $faturakalem->IRSALIYE_NO = NULL;
                            $faturakalem->IRSALIYE_TARIH = NULL;
                            $faturakalem->STHAR_TESTAR = NULL;
                            $faturakalem->OLCUBR = 1;
                            $faturakalem->VADE_TARIHI = $stokgiriscikis->tarih;
                            $faturakalem->SUBE_KODU = $subekodu;
                            $faturakalem->MUH_KODU = $kalem['urun'][0]['muhkodu'];
                            $faturakalem->C_YEDEK6 = 'X';
                            $faturakalem->D_YEDEK10 = $stokgiriscikis->tarih;
                            $faturakalem->PROJE_KODU = $stokgiriscikis->projekodu;
                            $faturakalem->DUZELTMETARIHI = date('Y-m-d H:i:s');
                            $faturakalem->STRA_IRSKONT = 0;
                            $faturakalem->save();
                            $id = DB::connection('sqlsrv2')->select("SELECT TOP(1)INCKEYNO FROM tblsthar WHERE SUBE_KODU=" . $subekodu . " AND FISNO='" . BackendController::ReverseTrk($fisno) . "' and STHAR_FTIRSIP='9' and STHAR_CARIKOD='" . BackendController::ReverseTrk($stokgiriscikis->masrafkodu) . "' ORDER BY INCKEYNO DESC");
                            $inckeyno = ($id[0]->INCKEYNO);
                            if (in_array($inckeyno, $inckey)) {
                                return array('durum' => '0', 'text' => 'Ambar Girişine Ait Kalemler Kısmı Kaydedilemedi');
                            } else { //kaydetme başarılı
                                array_push($inckey, $inckeyno);
                                $inckeyler .= ($inckeyler == "" ? "" : ",") . $inckeyno;
                            }
                        }
                    } catch (Exception $e) {
                        Log::error($e);
                        return array('durum' => '0', 'text' => 'Ambar Girişine Ait Kalemler Kısmı Kaydedilemedi');
                    }
                    return array('durum' => '1', 'fatura' => $fatura, 'faturaek' => $faturaek, 'faturakalemler' => $inckey, 'inckeylist' => $inckeyler);
                } catch (Exception $e) {
                    Log::error($e);
                    return array('durum' => '0', 'text' => 'Ambar Girişine Ait Açıklama Kısmı Kaydedilemedi');
                }
            } else { //ambar çıkışı
                $fatura = Fatuirs::where('SUBE_KODU', $subekodu)->where('FTIRSIP', '8')->where('FATIRS_NO', BackendController::ReverseTrk($fisno))
                    ->where('CARI_KODU', BackendController::ReverseTrk($stokgiriscikis->masrafkodu))->first();
                if ($fatura)
                    return array('durum' => '0', 'text' => 'Bu Fatura Numarası Bu Cari için Sistemde Kayıtlı');

                $fatura = new Fatuirs;
                $fatura->SUBE_KODU = $subekodu;
                $fatura->FATIRS_NO = BackendController::ReverseTrk($fisno);  //ornek A00000000633911
                $fatura->FTIRSIP = '8';    // 8 AMBAR ÇIKIŞI
                $fatura->CARI_KODU = $stokgiriscikis->masrafkodu;
                $fatura->TARIH = $stokgiriscikis->tarih;
                $fatura->TIPI = 0;
                $fatura->BRUTTUTAR = 0;
                $fatura->KDV = 0;
                $fatura->GENELTOPLAM = 0; //genel toplam
                $fatura->ACIKLAMA = NULL;
                $fatura->KOD1 = 0;
                $fatura->ODEMETARIHI = $stokgiriscikis->tarih;
                $fatura->KDV_DAHILMI = 'E';
                $fatura->FATKALEM_ADEDI = $kalemadedi; //kalem adedi
                $fatura->SIPARIS_TEST = $stokgiriscikis->tarih;
                $fatura->YEDEK22 = $stokgiriscikis->harekettur;
                $fatura->YEDEK = 'X';
                $fatura->PLA_KODU = NULL;
                $fatura->DOVIZTIP = 0;
                $fatura->DOVIZTUT = 0;
                $fatura->KS_KODU = $kasakodu;
                $fatura->F_YEDEK4 = 0;
                $fatura->C_YEDEK6 = 'M';
                $fatura->D_YEDEK10 = $stokgiriscikis->tarih;
                $fatura->PROJE_KODU = $stokgiriscikis->projekodu;
                $fatura->KAYITYAPANKUL = $servisyetkili->netsiskullanici;
                $fatura->KAYITTARIHI = date('Y-m-d H:i:s');
                $fatura->GELSUBE_KODU = 0;
                $fatura->GITSUBE_KODU = 0;
                $fatura->ISLETME_KODU = 1;
                $fatura->save();
                try {
                    $faturaek = new Fatuek;
                    $faturaek->SUBE_KODU = $subekodu;
                    $faturaek->FKOD = '8';
                    $faturaek->FATIRSNO = BackendController::ReverseTrk($fisno);
                    $faturaek->CKOD = BackendController::ReverseTrk($stokgiriscikis->masrafkodu);
                    $faturaek->ACIK1 = BackendController::ReverseTrk($stokgiriscikis->aciklama);
                    $faturaek->ACIK2 = BackendController::ReverseTrk($stokgiriscikis->aciklama2);
                    $faturaek->ACIK3 = BackendController::ReverseTrk($stokgiriscikis->aciklama3);
                    $faturaek->save();
                    $i = 1;
                    try {
                        foreach ($kalemler as $kalem) {
                            $faturakalem = new Sthar;
                            $urun = SubeUrun::find($kalem['urunid']);
                            if (!$urun) {
                                $fatura->delete();
                                $faturaek->delete();
                                Sthar::where(['SUBE_KODU' => $subekodu, 'FISNO' => BackendController::ReverseTrk($fisno), 'STHAR_FTIRSIP' => '8',
                                    'CARI_KODU' => $stokgiriscikis->masrafkodu])->delete();
                                return array('durum' => '0', 'text' => 'Stok Hareketine Ait Kalem Bilgisi Bulunamadı.');
                            }
                            $faturakalem->STOK_KODU = $kalem['urun'][0]['stokkodu'];
                            $faturakalem->FISNO = BackendController::ReverseTrk($fisno);
                            $faturakalem->STHAR_GCMIK = $kalem['miktar'];
                            $faturakalem->STHAR_GCKOD = 'C';
                            $faturakalem->STHAR_TARIH = $stokgiriscikis->tarih;
                            $faturakalem->STHAR_NF = 0;
                            $faturakalem->STHAR_BF = 0;
                            $faturakalem->STHAR_KDV = 0;
                            $faturakalem->STHAR_DOVTIP = 0;
                            $faturakalem->STHAR_DOVFIAT = 0;
                            $faturakalem->DEPO_KODU = $kalem['urun'][0]['depokodu'];
                            $faturakalem->STHAR_ACIKLAMA = BackendController::ReverseTrk($stokgiriscikis->masrafkodu);
                            $faturakalem->STHAR_FTIRSIP = '8';
                            $faturakalem->LISTE_FIAT = 1;
                            $faturakalem->STHAR_HTUR = $stokgiriscikis->harekettur;
                            $faturakalem->STHAR_ODEGUN = 0;
                            $faturakalem->STHAR_BGTIP = 'I';
                            $faturakalem->STHAR_KOD1 = 0;
                            $faturakalem->STHAR_KOD2 = 'M';
                            $faturakalem->STHAR_CARIKOD = BackendController::ReverseTrk($stokgiriscikis->masrafkodu);
                            $faturakalem->STHAR_SIP_TURU = 'M';
                            $faturakalem->PLASIYER_KODU = NULL;
                            $faturakalem->EKALAN = BackendController::ReverseTrk($stokgiriscikis->masrafkodu);;
                            $faturakalem->SIRA = $i;
                            $faturakalem->STRA_SIPKONT = 0;
                            $faturakalem->IRSALIYE_NO = NULL;
                            $faturakalem->IRSALIYE_TARIH = NULL;
                            $faturakalem->STHAR_TESTAR = NULL;
                            $faturakalem->OLCUBR = 1;
                            $faturakalem->VADE_TARIHI = $stokgiriscikis->tarih;
                            $faturakalem->SUBE_KODU = $subekodu;
                            $faturakalem->MUH_KODU = $kalem['urun'][0]['muhkodu'];
                            $faturakalem->C_YEDEK6 = 'X';
                            $faturakalem->D_YEDEK10 = $stokgiriscikis->tarih;
                            $faturakalem->PROJE_KODU = $stokgiriscikis->projekodu;
                            $faturakalem->DUZELTMETARIHI = date('Y-m-d H:i:s');
                            $faturakalem->STRA_IRSKONT = 0;
                            $faturakalem->save();
                            $id = DB::connection('sqlsrv2')->select("SELECT TOP(1)INCKEYNO FROM tblsthar WHERE SUBE_KODU=" . $subekodu . " AND FISNO='" . BackendController::ReverseTrk($fisno) . "' and STHAR_FTIRSIP='8' and STHAR_CARIKOD='" . BackendController::ReverseTrk($stokgiriscikis->masrafkodu) . "' ORDER BY INCKEYNO DESC");
                            $inckeyno = ($id[0]->INCKEYNO);
                            if (in_array($inckeyno, $inckey)) {
                                return array('durum' => '0', 'text' => 'Ambar Çıkışına Ait Kalemler Kısmı Kaydedilemedi');
                            } else { //kaydetme başarılı
                                array_push($inckey, $inckeyno);
                                $inckeyler .= ($inckeyler == "" ? "" : ",") . $inckeyno;
                            }
                        }
                    } catch (Exception $e) {
                        Log::error($e);
                        return array('durum' => '0', 'text' => 'Ambar Çıkışına Ait Kalemler Kısmı Kaydedilemedi');
                    }
                    return array('durum' => '1', 'fatura' => $fatura, 'faturaek' => $faturaek, 'faturakalemler' => $inckey, 'inckeylist' => $inckeyler);
                } catch (Exception $e) {
                    Log::error($e);
                    return array('durum' => '0', 'text' => 'Ambar Çıkışına Ait Açıklama Kısmı Kaydedilemedi');
                }
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return array('durum' => '0', 'text' => 'Girilen Sayaçlar için Fatura Numarası Alınamadı');
        }
    }

    public static function NetsisSubeStokHareketDuzenle($stokgiriscikis,$kalemler)
    {

        $subekodu = $stokgiriscikis->subekodu;
        $servisyetkili = ServisYetkili::where('kullanici_id', Auth::user()->id)->first();
        $kalemadedi = count($kalemler);
        $kasakod = KasaKod::where('subekodu', $subekodu)->orderBy('kasaadi', 'asc')->first();
        if (!$kasakod) {
            return array('durum' => '0', 'text' => 'Şube için Kasa Kodları Bulunamadı!');
        }
        $kasakodu = $kasakod->kasakod;
        if (!$servisyetkili) {
            return array('durum' => '0', 'text' => 'Bu Şube İçin Stok Hareketi Ekleme Yetkiniz Yok');
        }
        $dbname = $stokgiriscikis->db_name;
        $fisno = $stokgiriscikis->fisno;
        $inckey = array();
        $inckeyler = "";
        try {
            if ($dbname != 'MANAS' . date('Y')) { //eski depo girişi guncelleniyorsa
                return array('durum' => '0', 'text' => 'Eski Stok Hareketleri Güncellenemez ya da Silinemez!');
            }
            $inckeylist = explode(',', $stokgiriscikis->inckeyno);
            if ($stokgiriscikis->gckod == "G") { // ambar girişi
                $fatura = Fatuirs::where('SUBE_KODU', $subekodu)->where('FTIRSIP', '9')->where('FATIRS_NO', BackendController::ReverseTrk($fisno))
                    ->where('CARI_KODU', BackendController::ReverseTrk($stokgiriscikis->masrafkodu))->first();
                if (!$fatura)
                    return array('durum' => '0', 'text' => 'Bu Fatura Numarası Bulunamadı');

                $fatura->SUBE_KODU = $subekodu;
                $fatura->FATIRS_NO = BackendController::ReverseTrk($fisno);  //ornek A00000000633911
                $fatura->FTIRSIP = '9';    // 9 AMBAR GİRİŞİ
                $fatura->CARI_KODU = $stokgiriscikis->masrafkodu;
                $fatura->TARIH = $stokgiriscikis->tarih;
                $fatura->TIPI = 0;
                $fatura->BRUTTUTAR = 0;
                $fatura->KDV = 0;
                $fatura->GENELTOPLAM = 0; //genel toplam
                $fatura->ACIKLAMA = NULL;
                $fatura->KOD1 = 0;
                $fatura->ODEMETARIHI = $stokgiriscikis->tarih;
                $fatura->KDV_DAHILMI = 'E';
                $fatura->FATKALEM_ADEDI = $kalemadedi; //kalem adedi
                $fatura->SIPARIS_TEST = $stokgiriscikis->tarih;
                $fatura->YEDEK22 = $stokgiriscikis->harekettur;
                $fatura->YEDEK = 'X';
                $fatura->PLA_KODU = NULL;
                $fatura->DOVIZTIP = 0;
                $fatura->DOVIZTUT = 0;
                $fatura->KS_KODU = $kasakodu;
                $fatura->F_YEDEK4 = 0;
                $fatura->C_YEDEK6 = 'M';
                $fatura->D_YEDEK10 = $stokgiriscikis->tarih;
                $fatura->PROJE_KODU = $stokgiriscikis->projekodu;
                $fatura->KAYITYAPANKUL = $servisyetkili->netsiskullanici;
                $fatura->KAYITTARIHI = date('Y-m-d H:i:s');
                $fatura->GELSUBE_KODU = 0;
                $fatura->GITSUBE_KODU = 0;
                $fatura->ISLETME_KODU = 1;
                $fatura->save();
                try {
                    $faturaek = Fatuek::where('SUBE_KODU', $subekodu)->where('FKOD', '9')
                        ->where('FATIRSNO', BackendController::ReverseTrk($fisno))->first();
                    $faturaek->SUBE_KODU = $subekodu;
                    $faturaek->FKOD = '9';
                    $faturaek->FATIRSNO = BackendController::ReverseTrk($fisno);
                    $faturaek->CKOD = BackendController::ReverseTrk($stokgiriscikis->masrafkodu);
                    $faturaek->ACIK1 = BackendController::ReverseTrk($stokgiriscikis->aciklama);
                    $faturaek->ACIK2 = BackendController::ReverseTrk($stokgiriscikis->aciklama2);
                    $faturaek->ACIK3 = BackendController::ReverseTrk($stokgiriscikis->aciklama3);
                    $faturaek->save();
                    $i = 1;
                    try {
                        foreach ($kalemler as $kalem) {
                            $inckeyno = null;
                            foreach ($inckeylist as $inckey_) {
                                $fkalem = Sthar::find($inckey_);
                                if ($fkalem) {
                                    if ($kalem['urun'][0]['stokkodu'] == $fkalem->STOK_KODU) {
                                        $inckeyno = $inckey_;
                                        break;
                                    }
                                }
                            }
                            $urun = SubeUrun::find($kalem['urunid']);
                            if (!$urun) {
                                $fatura->delete();
                                $faturaek->delete();
                                Sthar::where(['SUBE_KODU' => $subekodu, 'FISNO' => BackendController::ReverseTrk($fisno), 'STHAR_FTIRSIP' => '9',
                                    'CARI_KODU' => $stokgiriscikis->masrafkodu])->delete();
                                return array('durum' => '0', 'text' => 'Stok Hareketine Ait Kalem Bilgisi Bulunamadı.');
                            }
                            if (is_null($inckeyno)) { // yeni kalem
                                $faturakalem = new Sthar;
                            } else {
                                $faturakalem = Sthar::find($inckeyno);
                            }
                            $faturakalem->STOK_KODU = $kalem['urun'][0]['stokkodu'];
                            $faturakalem->FISNO = BackendController::ReverseTrk($fisno);
                            $faturakalem->STHAR_GCMIK = $kalem['miktar'];
                            $faturakalem->STHAR_GCKOD = 'G';
                            $faturakalem->STHAR_TARIH = $stokgiriscikis->tarih;
                            $faturakalem->STHAR_NF = 0;
                            $faturakalem->STHAR_BF = 0;
                            $faturakalem->STHAR_KDV = 0;
                            $faturakalem->STHAR_DOVTIP = 0;
                            $faturakalem->STHAR_DOVFIAT = 0;
                            $faturakalem->DEPO_KODU = $kalem['urun'][0]['depokodu'];
                            $faturakalem->STHAR_ACIKLAMA = BackendController::ReverseTrk($stokgiriscikis->masrafkodu);
                            $faturakalem->STHAR_FTIRSIP = '9';
                            $faturakalem->LISTE_FIAT = 0;
                            $faturakalem->STHAR_HTUR = $stokgiriscikis->harekettur;
                            $faturakalem->STHAR_ODEGUN = 0;
                            $faturakalem->STHAR_BGTIP = 'I';
                            $faturakalem->STHAR_KOD1 = NULL;
                            $faturakalem->STHAR_KOD2 = 'M';
                            $faturakalem->STHAR_CARIKOD = BackendController::ReverseTrk($stokgiriscikis->masrafkodu);
                            $faturakalem->STHAR_SIP_TURU = 'M';
                            $faturakalem->PLASIYER_KODU = NULL;
                            $faturakalem->EKALAN = BackendController::ReverseTrk($stokgiriscikis->masrafkodu);;
                            $faturakalem->SIRA = $i;
                            $faturakalem->STRA_SIPKONT = 0;
                            $faturakalem->IRSALIYE_NO = NULL;
                            $faturakalem->IRSALIYE_TARIH = NULL;
                            $faturakalem->STHAR_TESTAR = NULL;
                            $faturakalem->OLCUBR = 1;
                            $faturakalem->VADE_TARIHI = $stokgiriscikis->tarih;
                            $faturakalem->SUBE_KODU = $subekodu;
                            $faturakalem->MUH_KODU = $kalem['urun'][0]['muhkodu'];
                            $faturakalem->C_YEDEK6 = 'X';
                            $faturakalem->D_YEDEK10 = $stokgiriscikis->tarih;
                            $faturakalem->PROJE_KODU = $stokgiriscikis->projekodu;
                            $faturakalem->DUZELTMETARIHI = date('Y-m-d H:i:s');
                            $faturakalem->STRA_IRSKONT = 0;
                            $faturakalem->save();
                            if (in_array($inckeyno, $inckey)) {
                                return array('durum' => '0', 'text' => 'Ambar Girişine Ait Kalemler Kısmı Kaydedilemedi');
                            } else { //kaydetme başarılı
                                array_push($inckey, $inckeyno);
                                $inckeyler .= ($inckeyler == "" ? "" : ",") . $inckeyno;
                            }
                        }
                        foreach ($inckeylist as $inckey_) {
                            if (!in_array($inckey_, $inckey)) { // silinecek
                                Sthar::find($inckey_)->delete();
                            }
                        }
                    } catch (Exception $e) {
                        Log::error($e);
                        return array('durum' => '0', 'text' => 'Ambar Girişine Ait Kalemler Kısmı Kaydedilemedi');
                    }
                    return array('durum' => '1', 'fatura' => $fatura, 'faturaek' => $faturaek, 'faturakalemler' => $inckey, 'inckeylist' => $inckeyler);
                } catch (Exception $e) {
                    Log::error($e);
                    return array('durum' => '0', 'text' => 'Ambar Girişine Ait Açıklama Kısmı Kaydedilemedi');
                }
            } else { //ambar çıkışı
                $fatura = Fatuirs::where('SUBE_KODU', $subekodu)->where('FTIRSIP', '8')->where('FATIRS_NO', BackendController::ReverseTrk($fisno))
                    ->where('CARI_KODU', BackendController::ReverseTrk($stokgiriscikis->masrafkodu))->first();
                if (!$fatura)
                    return array('durum' => '0', 'text' => 'Bu Fatura Numarası Bulunamadı');

                $fatura->SUBE_KODU = $subekodu;
                $fatura->FATIRS_NO = BackendController::ReverseTrk($fisno);  //ornek A00000000633911
                $fatura->FTIRSIP = '8';    // 8 AMBAR ÇIKIŞI
                $fatura->CARI_KODU = $stokgiriscikis->masrafkodu;
                $fatura->TARIH = $stokgiriscikis->tarih;
                $fatura->TIPI = 0;
                $fatura->BRUTTUTAR = 0;
                $fatura->KDV = 0;
                $fatura->GENELTOPLAM = 0; //genel toplam
                $fatura->ACIKLAMA = NULL;
                $fatura->KOD1 = 0;
                $fatura->ODEMETARIHI = $stokgiriscikis->tarih;
                $fatura->KDV_DAHILMI = 'E';
                $fatura->FATKALEM_ADEDI = $kalemadedi; //kalem adedi
                $fatura->SIPARIS_TEST = $stokgiriscikis->tarih;
                $fatura->YEDEK22 = $stokgiriscikis->harekettur;
                $fatura->YEDEK = 'X';
                $fatura->PLA_KODU = NULL;
                $fatura->DOVIZTIP = 0;
                $fatura->DOVIZTUT = 0;
                $fatura->KS_KODU = $kasakodu;
                $fatura->F_YEDEK4 = 0;
                $fatura->C_YEDEK6 = 'M';
                $fatura->D_YEDEK10 = $stokgiriscikis->tarih;
                $fatura->PROJE_KODU = $stokgiriscikis->projekodu;
                $fatura->KAYITYAPANKUL = $servisyetkili->netsiskullanici;
                $fatura->KAYITTARIHI = date('Y-m-d H:i:s');
                $fatura->GELSUBE_KODU = 0;
                $fatura->GITSUBE_KODU = 0;
                $fatura->ISLETME_KODU = 1;
                $fatura->save();
                try {
                    $faturaek = Fatuek::where('SUBE_KODU', $subekodu)->where('FKOD', '8')
                        ->where('FATIRSNO', BackendController::ReverseTrk($fisno))->first();
                    $faturaek->SUBE_KODU = $subekodu;
                    $faturaek->FKOD = '8';
                    $faturaek->FATIRSNO = BackendController::ReverseTrk($fisno);
                    $faturaek->CKOD = BackendController::ReverseTrk($stokgiriscikis->masrafkodu);
                    $faturaek->ACIK1 = BackendController::ReverseTrk($stokgiriscikis->aciklama);
                    $faturaek->ACIK2 = BackendController::ReverseTrk($stokgiriscikis->aciklama2);
                    $faturaek->ACIK3 = BackendController::ReverseTrk($stokgiriscikis->aciklama3);
                    $faturaek->save();
                    $i = 1;
                    try {
                        foreach ($kalemler as $kalem) {
                            $inckeyno = null;
                            foreach ($inckeylist as $inckey_) {
                                $fkalem = Sthar::find($inckey_);
                                if ($fkalem) {
                                    if ($kalem['urun'][0]['stokkodu'] == $fkalem->STOK_KODU) {
                                        $inckeyno = $inckey_;
                                        break;
                                    }
                                }
                            }
                            $urun = SubeUrun::find($kalem['urunid']);
                            if (!$urun) {
                                $fatura->delete();
                                $faturaek->delete();
                                Sthar::where(['SUBE_KODU' => $subekodu, 'FISNO' => BackendController::ReverseTrk($fisno), 'STHAR_FTIRSIP' => '8',
                                    'CARI_KODU' => $stokgiriscikis->masrafkodu])->delete();
                                return array('durum' => '0', 'text' => 'Stok Hareketine Ait Kalem Bilgisi Bulunamadı.');
                            }
                            if (is_null($inckeyno)) { // yeni kalem
                                $faturakalem = new Sthar;
                            } else {
                                $faturakalem = Sthar::find($inckeyno);
                            }
                            $faturakalem->STOK_KODU = $kalem['urun'][0]['stokkodu'];
                            $faturakalem->FISNO = BackendController::ReverseTrk($fisno);
                            $faturakalem->STHAR_GCMIK = $kalem['miktar'];
                            $faturakalem->STHAR_GCKOD = 'C';
                            $faturakalem->STHAR_TARIH = $stokgiriscikis->tarih;
                            $faturakalem->STHAR_NF = 0;
                            $faturakalem->STHAR_BF = 0;
                            $faturakalem->STHAR_KDV = 0;
                            $faturakalem->STHAR_DOVTIP = 0;
                            $faturakalem->STHAR_DOVFIAT = 0;
                            $faturakalem->DEPO_KODU = $kalem['urun'][0]['depokodu'];
                            $faturakalem->STHAR_ACIKLAMA = BackendController::ReverseTrk($stokgiriscikis->masrafkodu);
                            $faturakalem->STHAR_FTIRSIP = '8';
                            $faturakalem->LISTE_FIAT = 1;
                            $faturakalem->STHAR_HTUR = $stokgiriscikis->harekettur;
                            $faturakalem->STHAR_ODEGUN = 0;
                            $faturakalem->STHAR_BGTIP = 'I';
                            $faturakalem->STHAR_KOD1 = 0;
                            $faturakalem->STHAR_KOD2 = 'M';
                            $faturakalem->STHAR_CARIKOD = BackendController::ReverseTrk($stokgiriscikis->masrafkodu);
                            $faturakalem->STHAR_SIP_TURU = 'M';
                            $faturakalem->PLASIYER_KODU = NULL;
                            $faturakalem->EKALAN = BackendController::ReverseTrk($stokgiriscikis->masrafkodu);;
                            $faturakalem->SIRA = $i;
                            $faturakalem->STRA_SIPKONT = 0;
                            $faturakalem->IRSALIYE_NO = NULL;
                            $faturakalem->IRSALIYE_TARIH = NULL;
                            $faturakalem->STHAR_TESTAR = NULL;
                            $faturakalem->OLCUBR = 1;
                            $faturakalem->VADE_TARIHI = $stokgiriscikis->tarih;
                            $faturakalem->SUBE_KODU = $subekodu;
                            $faturakalem->MUH_KODU = $kalem['urun'][0]['muhkodu'];
                            $faturakalem->C_YEDEK6 = 'X';
                            $faturakalem->D_YEDEK10 = $stokgiriscikis->tarih;
                            $faturakalem->PROJE_KODU = $stokgiriscikis->projekodu;
                            $faturakalem->DUZELTMETARIHI = date('Y-m-d H:i:s');
                            $faturakalem->STRA_IRSKONT = 0;
                            $faturakalem->save();
                            if (in_array($inckeyno, $inckey)) {
                                return array('durum' => '0', 'text' => 'Ambar Çıkışına Ait Kalemler Kısmı Kaydedilemedi');
                            } else { //kaydetme başarılı
                                array_push($inckey, $inckeyno);
                                $inckeyler .= ($inckeyler == "" ? "" : ",") . $inckeyno;
                            }
                        }
                        foreach ($inckeylist as $inckey_) {
                            if (!in_array($inckey_, $inckey)) { // silinecek
                                Sthar::find($inckey_)->delete();
                            }
                        }
                    } catch (Exception $e) {
                        Log::error($e);
                        return array('durum' => '0', 'text' => 'Ambar Çıkışına Ait Kalemler Kısmı Kaydedilemedi');
                    }
                    return array('durum' => '1', 'fatura' => $fatura, 'faturaek' => $faturaek, 'faturakalemler' => $inckey, 'inckeylist' => $inckeyler);
                } catch (Exception $e) {
                    Log::error($e);
                    return array('durum' => '0', 'text' => 'Ambar Çıkışına Ait Açıklama Kısmı Kaydedilemedi');
                }
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return array('durum' => '0', 'text' => 'Girilen Sayaçlar için Fatura Numarası Alınamadı');
        }
    }

    public static function NetsisSubeStokHareketGerial($stokgiriscikis,$kalemler)
    {
        $subekodu = $stokgiriscikis->subekodu;
        $servisyetkili = ServisYetkili::where('kullanici_id', Auth::user()->id)->first();
        $kalemadedi = count($kalemler);
        $kasakod = KasaKod::where('subekodu', $subekodu)->orderBy('kasaadi', 'asc')->first();
        if (!$kasakod) {
            return array('durum' => '0', 'text' => 'Şube için Kasa Kodları Bulunamadı!');
        }
        $kasakodu = $kasakod->kasakod;
        if (!$servisyetkili) {
            return array('durum' => '0', 'text' => 'Bu Şube İçin Stok Hareketi Ekleme Yetkiniz Yok');
        }
        $dbname = $stokgiriscikis->db_name;
        $fisno = $stokgiriscikis->fisno;
        $inckey = array();
        $inckeyler = "";
        try {
            if ($dbname != 'MANAS' . date('Y')) { //eski depo girişi guncelleniyorsa
                return array('durum' => '0', 'text' => 'Eski Stok Hareketleri Güncellenemez ya da Silinemez!');
            }
            $inckeylist = explode(',', $stokgiriscikis->inckeyno);
            if ($stokgiriscikis->gckod == "G") { // ambar girişi
                $fatura = Fatuirs::where('SUBE_KODU', $subekodu)->where('FTIRSIP', '9')->where('FATIRS_NO', BackendController::ReverseTrk($fisno))
                    ->where('CARI_KODU', BackendController::ReverseTrk($stokgiriscikis->masrafkodu))->first();
                if (!$fatura)
                    return array('durum' => '0', 'text' => 'Bu Fatura Numarası Bulunamadı');

                $fatura->SUBE_KODU = $subekodu;
                $fatura->FATIRS_NO = BackendController::ReverseTrk($fisno);  //ornek A00000000633911
                $fatura->FTIRSIP = '9';    // 9 AMBAR GİRİŞİ
                $fatura->CARI_KODU = $stokgiriscikis->masrafkodu;
                $fatura->TARIH = $stokgiriscikis->tarih;
                $fatura->TIPI = 0;
                $fatura->BRUTTUTAR = 0;
                $fatura->KDV = 0;
                $fatura->GENELTOPLAM = 0; //genel toplam
                $fatura->ACIKLAMA = NULL;
                $fatura->KOD1 = 0;
                $fatura->ODEMETARIHI = $stokgiriscikis->tarih;
                $fatura->KDV_DAHILMI = 'E';
                $fatura->FATKALEM_ADEDI = $kalemadedi; //kalem adedi
                $fatura->SIPARIS_TEST = $stokgiriscikis->tarih;
                $fatura->YEDEK22 = $stokgiriscikis->harekettur;
                $fatura->YEDEK = 'X';
                $fatura->PLA_KODU = NULL;
                $fatura->DOVIZTIP = 0;
                $fatura->DOVIZTUT = 0;
                $fatura->KS_KODU = $kasakodu;
                $fatura->F_YEDEK4 = 0;
                $fatura->C_YEDEK6 = 'M';
                $fatura->D_YEDEK10 = $stokgiriscikis->tarih;
                $fatura->PROJE_KODU = $stokgiriscikis->projekodu;
                $fatura->KAYITYAPANKUL = $servisyetkili->netsiskullanici;
                $fatura->KAYITTARIHI = date('Y-m-d H:i:s');
                $fatura->GELSUBE_KODU = 0;
                $fatura->GITSUBE_KODU = 0;
                $fatura->ISLETME_KODU = 1;
                $fatura->save();
                try {
                    $faturaek = Fatuek::where('SUBE_KODU', $subekodu)->where('FKOD', '9')
                        ->where('FATIRSNO', BackendController::ReverseTrk($fisno))->first();
                    $faturaek->SUBE_KODU = $subekodu;
                    $faturaek->FKOD = '9';
                    $faturaek->FATIRSNO = BackendController::ReverseTrk($fisno);
                    $faturaek->CKOD = BackendController::ReverseTrk($stokgiriscikis->masrafkodu);
                    $faturaek->ACIK1 = BackendController::ReverseTrk($stokgiriscikis->aciklama);
                    $faturaek->ACIK2 = BackendController::ReverseTrk($stokgiriscikis->aciklama2);
                    $faturaek->ACIK3 = BackendController::ReverseTrk($stokgiriscikis->aciklama3);
                    $faturaek->save();
                    $i = 1;
                    try {
                        foreach ($kalemler as $kalem) {
                            $inckeyno = null;
                            foreach ($inckeylist as $inckey_) {
                                $fkalem = Sthar::find($inckey_);
                                if ($fkalem) {
                                    if ($kalem['urun'][0]['stokkodu'] == $fkalem->STOK_KODU) {
                                        $inckeyno = $inckey_;
                                        break;
                                    }
                                }
                            }
                            $urun = SubeUrun::find($kalem['urunid']);
                            if (!$urun) {
                                $fatura->delete();
                                $faturaek->delete();
                                Sthar::where(['SUBE_KODU' => $subekodu, 'FISNO' => BackendController::ReverseTrk($fisno), 'STHAR_FTIRSIP' => '9',
                                    'CARI_KODU' => $stokgiriscikis->masrafkodu])->delete();
                                return array('durum' => '0', 'text' => 'Stok Hareketine Ait Ürün Bilgisi Bulunamadı.');
                            }
                            if (is_null($inckeyno)) { // yeni kalem
                                continue;
                            } else {
                                $faturakalem = Sthar::find($inckeyno);
                            }
                            $faturakalem->STOK_KODU = $kalem['urun'][0]['stokkodu'];
                            $faturakalem->FISNO = BackendController::ReverseTrk($fisno);
                            $faturakalem->STHAR_GCMIK = $kalem['miktar'];
                            $faturakalem->STHAR_GCKOD = 'G';
                            $faturakalem->STHAR_TARIH = $stokgiriscikis->tarih;
                            $faturakalem->STHAR_NF = 0;
                            $faturakalem->STHAR_BF = 0;
                            $faturakalem->STHAR_KDV = 0;
                            $faturakalem->STHAR_DOVTIP = 0;
                            $faturakalem->STHAR_DOVFIAT = 0;
                            $faturakalem->DEPO_KODU = $kalem['urun'][0]['depokodu'];
                            $faturakalem->STHAR_ACIKLAMA = BackendController::ReverseTrk($stokgiriscikis->masrafkodu);
                            $faturakalem->STHAR_FTIRSIP = '9';
                            $faturakalem->LISTE_FIAT = 0;
                            $faturakalem->STHAR_HTUR = $stokgiriscikis->harekettur;
                            $faturakalem->STHAR_ODEGUN = 0;
                            $faturakalem->STHAR_BGTIP = 'I';
                            $faturakalem->STHAR_KOD1 = NULL;
                            $faturakalem->STHAR_KOD2 = 'M';
                            $faturakalem->STHAR_CARIKOD = BackendController::ReverseTrk($stokgiriscikis->masrafkodu);
                            $faturakalem->STHAR_SIP_TURU = 'M';
                            $faturakalem->PLASIYER_KODU = NULL;
                            $faturakalem->EKALAN = BackendController::ReverseTrk($stokgiriscikis->masrafkodu);;
                            $faturakalem->SIRA = $i;
                            $faturakalem->STRA_SIPKONT = 0;
                            $faturakalem->IRSALIYE_NO = NULL;
                            $faturakalem->IRSALIYE_TARIH = NULL;
                            $faturakalem->STHAR_TESTAR = NULL;
                            $faturakalem->OLCUBR = 1;
                            $faturakalem->VADE_TARIHI = $stokgiriscikis->tarih;
                            $faturakalem->SUBE_KODU = $subekodu;
                            $faturakalem->MUH_KODU = $kalem['urun'][0]['muhkodu'];
                            $faturakalem->C_YEDEK6 = 'X';
                            $faturakalem->D_YEDEK10 = $stokgiriscikis->tarih;
                            $faturakalem->PROJE_KODU = $stokgiriscikis->projekodu;
                            $faturakalem->DUZELTMETARIHI = date('Y-m-d H:i:s');
                            $faturakalem->STRA_IRSKONT = 0;
                            $faturakalem->save();
                            if (in_array($inckeyno, $inckey)) {
                                return array('durum' => '0', 'text' => 'Ambar Girişine Ait Kalemler Kısmı Kaydedilemedi');
                            } else { //kaydetme başarılı
                                array_push($inckey, $inckeyno);
                                $inckeyler .= ($inckeyler == "" ? "" : ",") . $inckeyno;
                            }
                        }
                        foreach ($inckeylist as $inckey_) {
                            if (!in_array($inckey_, $inckey)) { // silinecek
                                Sthar::find($inckey_)->delete();
                            }
                        }
                    } catch (Exception $e) {
                        Log::error($e);
                        return array('durum' => '0', 'text' => 'Ambar Girişine Ait Kalemler Kısmı Kaydedilemedi');
                    }
                    return array('durum' => '1', 'fatura' => $fatura, 'faturaek' => $faturaek, 'faturakalemler' => $inckey, 'inckeylist' => $inckeyler);
                } catch (Exception $e) {
                    Log::error($e);
                    return array('durum' => '0', 'text' => 'Ambar Girişine Ait Açıklama Kısmı Kaydedilemedi');
                }
            } else { //ambar çıkışı
                $fatura = Fatuirs::where('SUBE_KODU', $subekodu)->where('FTIRSIP', '8')->where('FATIRS_NO', BackendController::ReverseTrk($fisno))
                    ->where('CARI_KODU', BackendController::ReverseTrk($stokgiriscikis->masrafkodu))->first();
                if (!$fatura)
                    return array('durum' => '0', 'text' => 'Bu Fatura Numarası Bulunamadı');

                $fatura->SUBE_KODU = $subekodu;
                $fatura->FATIRS_NO = BackendController::ReverseTrk($fisno);  //ornek A00000000633911
                $fatura->FTIRSIP = '8';    // 8 AMBAR ÇIKIŞI
                $fatura->CARI_KODU = $stokgiriscikis->masrafkodu;
                $fatura->TARIH = $stokgiriscikis->tarih;
                $fatura->TIPI = 0;
                $fatura->BRUTTUTAR = 0;
                $fatura->KDV = 0;
                $fatura->GENELTOPLAM = 0; //genel toplam
                $fatura->ACIKLAMA = NULL;
                $fatura->KOD1 = 0;
                $fatura->ODEMETARIHI = $stokgiriscikis->tarih;
                $fatura->KDV_DAHILMI = 'E';
                $fatura->FATKALEM_ADEDI = $kalemadedi; //kalem adedi
                $fatura->SIPARIS_TEST = $stokgiriscikis->tarih;
                $fatura->YEDEK22 = $stokgiriscikis->harekettur;
                $fatura->YEDEK = 'X';
                $fatura->PLA_KODU = NULL;
                $fatura->DOVIZTIP = 0;
                $fatura->DOVIZTUT = 0;
                $fatura->KS_KODU = $kasakodu;
                $fatura->F_YEDEK4 = 0;
                $fatura->C_YEDEK6 = 'M';
                $fatura->D_YEDEK10 = $stokgiriscikis->tarih;
                $fatura->PROJE_KODU = $stokgiriscikis->projekodu;
                $fatura->KAYITYAPANKUL = $servisyetkili->netsiskullanici;
                $fatura->KAYITTARIHI = date('Y-m-d H:i:s');
                $fatura->GELSUBE_KODU = 0;
                $fatura->GITSUBE_KODU = 0;
                $fatura->ISLETME_KODU = 1;
                $fatura->save();
                try {
                    $faturaek = Fatuek::where('SUBE_KODU', $subekodu)->where('FKOD', '8')
                        ->where('FATIRSNO', BackendController::ReverseTrk($fisno))->first();
                    $faturaek->SUBE_KODU = $subekodu;
                    $faturaek->FKOD = '8';
                    $faturaek->FATIRSNO = BackendController::ReverseTrk($fisno);
                    $faturaek->CKOD = BackendController::ReverseTrk($stokgiriscikis->masrafkodu);
                    $faturaek->ACIK1 = BackendController::ReverseTrk($stokgiriscikis->aciklama);
                    $faturaek->ACIK2 = BackendController::ReverseTrk($stokgiriscikis->aciklama2);
                    $faturaek->ACIK3 = BackendController::ReverseTrk($stokgiriscikis->aciklama3);
                    $faturaek->save();
                    $i = 1;
                    try {
                        foreach ($kalemler as $kalem) {
                            $inckeyno = null;
                            foreach ($inckeylist as $inckey_) {
                                $fkalem = Sthar::find($inckey_);
                                if ($fkalem) {
                                    if ($kalem['urun'][0]['stokkodu'] == $fkalem->STOK_KODU) {
                                        $inckeyno = $inckey_;
                                        break;
                                    }
                                }
                            }
                            $urun = SubeUrun::find($kalem['urunid']);
                            if (!$urun) {
                                $fatura->delete();
                                $faturaek->delete();
                                Sthar::where(['SUBE_KODU' => $subekodu, 'FISNO' => BackendController::ReverseTrk($fisno), 'STHAR_FTIRSIP' => '8',
                                    'CARI_KODU' => $stokgiriscikis->masrafkodu])->delete();
                                return array('durum' => '0', 'text' => 'Stok Hareketine Ait Ürün Bilgisi Bulunamadı.');
                            }
                            if (is_null($inckeyno)) { // yeni kalem
                                continue;
                            } else {
                                $faturakalem = Sthar::find($inckeyno);
                            }
                            $faturakalem->STOK_KODU = $kalem['urun'][0]['stokkodu'];
                            $faturakalem->FISNO = BackendController::ReverseTrk($fisno);
                            $faturakalem->STHAR_GCMIK = $kalem['miktar'];
                            $faturakalem->STHAR_GCKOD = 'C';
                            $faturakalem->STHAR_TARIH = $stokgiriscikis->tarih;
                            $faturakalem->STHAR_NF = 0;
                            $faturakalem->STHAR_BF = 0;
                            $faturakalem->STHAR_KDV = 0;
                            $faturakalem->STHAR_DOVTIP = 0;
                            $faturakalem->STHAR_DOVFIAT = 0;
                            $faturakalem->DEPO_KODU = $kalem['urun'][0]['depokodu'];
                            $faturakalem->STHAR_ACIKLAMA = BackendController::ReverseTrk($stokgiriscikis->masrafkodu);
                            $faturakalem->STHAR_FTIRSIP = '8';
                            $faturakalem->LISTE_FIAT = 1;
                            $faturakalem->STHAR_HTUR = $stokgiriscikis->harekettur;
                            $faturakalem->STHAR_ODEGUN = 0;
                            $faturakalem->STHAR_BGTIP = 'I';
                            $faturakalem->STHAR_KOD1 = 0;
                            $faturakalem->STHAR_KOD2 = 'M';
                            $faturakalem->STHAR_CARIKOD = BackendController::ReverseTrk($stokgiriscikis->masrafkodu);
                            $faturakalem->STHAR_SIP_TURU = 'M';
                            $faturakalem->PLASIYER_KODU = NULL;
                            $faturakalem->EKALAN = BackendController::ReverseTrk($stokgiriscikis->masrafkodu);;
                            $faturakalem->SIRA = $i;
                            $faturakalem->STRA_SIPKONT = 0;
                            $faturakalem->IRSALIYE_NO = NULL;
                            $faturakalem->IRSALIYE_TARIH = NULL;
                            $faturakalem->STHAR_TESTAR = NULL;
                            $faturakalem->OLCUBR = 1;
                            $faturakalem->VADE_TARIHI = $stokgiriscikis->tarih;
                            $faturakalem->SUBE_KODU = $subekodu;
                            $faturakalem->MUH_KODU = $kalem['urun'][0]['muhkodu'];
                            $faturakalem->C_YEDEK6 = 'X';
                            $faturakalem->D_YEDEK10 = $stokgiriscikis->tarih;
                            $faturakalem->PROJE_KODU = $stokgiriscikis->projekodu;
                            $faturakalem->DUZELTMETARIHI = date('Y-m-d H:i:s');
                            $faturakalem->STRA_IRSKONT = 0;
                            $faturakalem->save();
                            if (in_array($inckeyno, $inckey)) {
                                return array('durum' => '0', 'text' => 'Ambar Çıkışına Ait Kalemler Kısmı Kaydedilemedi');
                            } else { //kaydetme başarılı
                                array_push($inckey, $inckeyno);
                                $inckeyler .= ($inckeyler == "" ? "" : ",") . $inckeyno;
                            }
                        }
                        foreach ($inckeylist as $inckey_) {
                            if (!in_array($inckey_, $inckey)) { // silinecek
                                Sthar::find($inckey_)->delete();
                            }
                        }
                    } catch (Exception $e) {
                        Log::error($e);
                        return array('durum' => '0', 'text' => 'Ambar Çıkışına Ait Kalemler Kısmı Kaydedilemedi');
                    }
                    return array('durum' => '1', 'fatura' => $fatura, 'faturaek' => $faturaek, 'faturakalemler' => $inckey, 'inckeylist' => $inckeyler);
                } catch (Exception $e) {
                    Log::error($e);
                    return array('durum' => '0', 'text' => 'Ambar Çıkışına Ait Açıklama Kısmı Kaydedilemedi');
                }
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return array('durum' => '0', 'text' => 'Girilen Sayaçlar için Fatura Numarası Alınamadı');
        }
    }

    public static function NetsisSubeStokHareketSil($stokgiriscikisid){
        $stokgiriscikis= StokGirisCikis::find($stokgiriscikisid);
        if($stokgiriscikis){
            $subekodu = $stokgiriscikis->subekodu;
            $servisyetkili = ServisYetkili::where('kullanici_id', Auth::user()->id)->first();
            if (!$servisyetkili) {
                return array('durum' => '0', 'text' => 'Bu Şube İçin Stok Hareketi Ekleme Yetkiniz Yok');
            }
            $dbname = $stokgiriscikis->db_name;
            $fisno = $stokgiriscikis->fisno;
            if($fisno) {
                try {
                    if ($dbname != 'MANAS' . date('Y')) { //eski depo girişi guncelleniyorsa
                        return array('durum' => '0', 'text' => 'Eski Stok Hareketleri Güncellenemez ya da Silinemez!');
                    }
                    $inckeylist = explode(',', $stokgiriscikis->inckeyno);
                    if ($stokgiriscikis->gckod == "G") { // ambar girişi
                        $fatura = Fatuirs::where('SUBE_KODU', $subekodu)->where('FTIRSIP', '9')
                            ->where('FATIRS_NO', BackendController::ReverseTrk($fisno))
                            ->where('CARI_KODU', BackendController::ReverseTrk($stokgiriscikis->masrafkodu))->first();
                        if (!$fatura)
                            return array('durum' => '0', 'text' => 'Bu Fatura Numarası Bulunamadı');
                        $fatura->delete();
                        try {
                            $faturaek = Fatuek::where('SUBE_KODU', $subekodu)->where('FKOD', '9')
                                ->where('FATIRSNO', BackendController::ReverseTrk($fisno))->first();
                            if (!$faturaek)
                                return array('durum' => '0', 'text' => 'Bu Faturanın Açıklama Kısmı Bulunamadı');
                            $faturaek->delete();
                            try {
                                foreach ($inckeylist as $inckey) {
                                    $faturakalem = Sthar::find($inckey);
                                    if (!$faturakalem)
                                        return array('durum' => '0', 'text' => 'Bu Faturanın Kalemleri Bulunamadı');
                                    $faturakalem->delete();
                                }
                            } catch (Exception $e) {
                                Log::error($e);
                                return array('durum' => '0', 'text' => 'Ambar Girişine Ait Kalemler Kısmı Silinemedi');
                            }
                            return array('durum' => '1');
                        } catch (Exception $e) {
                            Log::error($e);
                            return array('durum' => '0', 'text' => 'Ambar Girişine Ait Açıklama Kısmı Silinemedi');
                        }
                    } else { //ambar çıkışı
                        $fatura = Fatuirs::where('SUBE_KODU', $subekodu)->where('FTIRSIP', '8')
                            ->where('FATIRS_NO', BackendController::ReverseTrk($fisno))
                            ->where('CARI_KODU', BackendController::ReverseTrk($stokgiriscikis->masrafkodu))->first();
                        if (!$fatura)
                            return array('durum' => '0', 'text' => 'Bu Fatura Numarası Bulunamadı');
                        $fatura->delete();
                        try {
                            $faturaek = Fatuek::where('SUBE_KODU', $subekodu)->where('FKOD', '8')
                                ->where('FATIRSNO', BackendController::ReverseTrk($fisno))->first();
                            if (!$faturaek)
                                return array('durum' => '0', 'text' => 'Bu Faturanın Açıklama Kısmı Bulunamadı');
                            $faturaek->delete();
                            try {
                                foreach ($inckeylist as $inckey) {
                                    $faturakalem = Sthar::find($inckey);
                                    if (!$faturakalem)
                                        return array('durum' => '0', 'text' => 'Bu Faturanın Kalemleri Bulunamadı');
                                    $faturakalem->delete();
                                }
                            } catch (Exception $e) {
                                Log::error($e);
                                return array('durum' => '0', 'text' => 'Ambar Çıkışına Ait Kalemler Kısmı Silinemedi');
                            }
                            return array('durum' => '1');
                        } catch (Exception $e) {
                            Log::error($e);
                            return array('durum' => '0', 'text' => 'Ambar Çıkışına Ait Açıklama Kısmı Silinemedi');
                        }
                    }
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    return array('durum' => '0', 'text' => 'Stok Hareketi Silinemedi');
                }
            }else{
                return array('durum'=>'0','text'=>'Stok Hareketine Ait Fis Numarası Bulunamadı');
            }
        }else{
            return array('durum'=>'0','text'=>'Stok Hareketi Bulunamadı');
        }
    }

    public static function NetsisSubeStokHareketTemizle($stokgiriscikis)
    {
        $subekodu = $stokgiriscikis->subekodu;
        $dbname = $stokgiriscikis->db_name;
        $fisno = $stokgiriscikis->fisno;
        if ($fisno) {
            try {
                if ($dbname != 'MANAS' . date('Y')) { //eski depo girişi guncelleniyorsa
                    return array('durum' => '0', 'text' => 'Eski Stok Hareketleri Güncellenemez ya da Silinemez!');
                }
                $inckeylist = explode(',', $stokgiriscikis->inckeyno);
                if ($stokgiriscikis->gckod == "G") { // ambar girişi
                    $fatura = Fatuirs::where('SUBE_KODU', $subekodu)->where('FTIRSIP', '9')
                        ->where('FATIRS_NO', BackendController::ReverseTrk($fisno))
                        ->where('CARI_KODU', BackendController::ReverseTrk($stokgiriscikis->masrafkodu))->first();
                    if ($fatura)
                        $fatura->delete();
                    try {
                        $faturaek = Fatuek::where('SUBE_KODU', $subekodu)->where('FKOD', '9')
                            ->where('FATIRSNO', BackendController::ReverseTrk($fisno))->first();
                        if ($faturaek)
                            $faturaek->delete();
                        try {
                            foreach ($inckeylist as $inckey) {
                                $faturakalem = Sthar::find($inckey);
                                if ($faturakalem)
                                    $faturakalem->delete();
                            }
                        } catch (Exception $e) {
                            Log::error($e);
                            return array('durum' => '0', 'text' => 'Ambar Girişine Ait Kalemler Kısmı Silinemedi');
                        }
                        return array('durum' => '1');
                    } catch (Exception $e) {
                        Log::error($e);
                        return array('durum' => '0', 'text' => 'Ambar Girişine Ait Açıklama Kısmı Silinemedi');
                    }
                } else { //ambar çıkışı
                    $fatura = Fatuirs::where('SUBE_KODU', $subekodu)->where('FTIRSIP', '8')
                        ->where('FATIRS_NO', BackendController::ReverseTrk($fisno))
                        ->where('CARI_KODU', BackendController::ReverseTrk($stokgiriscikis->masrafkodu))->first();
                    if ($fatura)
                        $fatura->delete();
                    try {
                        $faturaek = Fatuek::where('SUBE_KODU', $subekodu)->where('FKOD', '8')
                            ->where('FATIRSNO', BackendController::ReverseTrk($fisno))->first();
                        if ($faturaek)
                            $faturaek->delete();
                        try {
                            foreach ($inckeylist as $inckey) {
                                $faturakalem = Sthar::find($inckey);
                                if ($faturakalem)
                                    $faturakalem->delete();
                            }
                        } catch (Exception $e) {
                            Log::error($e);
                            return array('durum' => '0', 'text' => 'Ambar Çıkışına Ait Kalemler Kısmı Silinemedi');
                        }
                        return array('durum' => '1');
                    } catch (Exception $e) {
                        Log::error($e);
                        return array('durum' => '0', 'text' => 'Ambar Çıkışına Ait Açıklama Kısmı Silinemedi');
                    }
                }
            } catch (Exception $e) {
                DB::rollBack();
                Log::error($e);
                return array('durum' => '0', 'text' => 'Stok Hareketi Silinemedi');
            }
        } else {
            return array('durum' => '0', 'text' => 'Stok Hareketine Ait Fis Numarası Bulunamadı');
        }
    }

    public static function SatisFaturasiSil($sayacsatis){ // Netsis Tarafında Satış Faturasını Karşılar
        try {

            //$sayacsatis = SubeSayacSatis::find($id);
            $fatirsno = $sayacsatis->faturano;
            $secilenler = explode(',', $sayacsatis->secilenler);
            $sayaclar = explode(';', $sayacsatis->sayaclar);
            $adetler = explode(',', $sayacsatis->adet);
            $birimfiyatlar = explode(',', $sayacsatis->birimfiyat);
            $ucretsizler = explode(',', $sayacsatis->ucretsiz);
            $urunler = array();
            $status = 0;
            if($sayacsatis->odemetipi==4 ||$sayacsatis->odemetipi==5 || $sayacsatis->odemetipi==6 ){
                $status = 1;
            }
            for ($i = 0; $i < count($secilenler); $i++) {
                $urun = SubeUrun::find($secilenler[$i]);
                $urun->adet = $adetler[$i];
                $urun->fiyat = $birimfiyatlar[$i];
                $urun->ucretsiz = $ucretsizler[$i];
                $urun->stokkodu = NetsisStokKod::find($urun->netsisstokkod_id);
                if ($urun->baglanti) {
                    $sayaclist = explode(',', $sayaclar[$i]);
                    $urun->sayaclar = AboneSayac::where('sayacadi_id', $urun->sayacadi_id)->where('sayaccap_id', $urun->sayaccap_id)
                        ->where('uretimyer_id', $sayacsatis->uretimyer_id)->whereIn('id', $sayaclist)->get();
                } else {
                    $urun->sayaclar = array();
                }
                array_push($urunler, $urun);
            }
            try {
                $fatura = Fatuirs::where('SUBE_KODU', $sayacsatis->subekodu)->where('FTIRSIP', 1)->where('FATIRS_NO', $fatirsno)->first();
                if (!$fatura) {
                    return 13;
                }
                $faturaek = Fatuek::where('SUBE_KODU', $sayacsatis->subekodu)->where('FKOD', 1)->where('FATIRSNO', $fatirsno)->first();
                if (!$faturaek) {
                    return 14;
                }

                $inckey = array();
                for ($i = 0; $i < count($urunler); $i++) {
                    $faturakalem = Sthar::where('SUBE_KODU', $sayacsatis->subekodu)->where('STHAR_FTIRSIP', 1)->where('FISNO', $fatirsno)
                        ->where('STOK_KODU',$urunler[$i]->stokkodu->kodu)->first();
                    if(!$faturakalem)
                        return 15;
                    array_push($inckey, $faturakalem->INCKEYNO);
                    $faturakalem->delete();
                }
                $faturaek->delete();
                $fatura->delete();

                $durum = BackendController::SeriSil($sayacsatis,$inckey);
                if ($durum != 1) {
                    return 16;
                }
                if (!$status) { //AÇIK FATURA KASA KAYIDI YAPILMIYOR
                    $durum = BackendController::KasaSil($sayacsatis, 1);
                    if ($durum != 1) {
                        return 17;
                    }
                }

                $durum = BackendController::CariHareketSil($sayacsatis);
                if ($durum != 1) {
                    return 18;
                }

                $durum = BackendController::MuhasebeFisSil($sayacsatis);
                if ($durum != 1) {
                    return 19;
                }

                $durum = BackendController::ServisBilgisiSil($sayacsatis, 0); // Servis Bilgisi Eklenecek 0 Satış 1 Tamir sonrası servis 2 arıza kontrolü
                if ($durum == 0 || $durum == 2 || $durum == 3 || $durum == 4 || $durum == 5) {
                    return 20;
                }
                return 1;
            } catch (Exception $e) {
                Log::error($e);
                return str_replace("'","\'",$e->getMessage());
            }
        } catch (Exception $e) {
            Log::error($e);
            return str_replace("'","\'",$e->getMessage());
        }
    }

    public static function ServisBilgisiSil($bilgi,$tipi){
        try {
            if($tipi==0){ //sayaç montaj
               // $sayacsatis = SubeSayacSatis::find($id);
                $abone = Abone::find($bilgi->abone_id);
                $sayaclar = explode(';', $bilgi->sayaclar);
                foreach ($sayaclar as $sayac) {
                    $sayaclist = explode(',', $sayac);
                    foreach ($sayaclist as $sayacid) {
                        if ($sayacid != 0) {
                            $abonesayac = AboneSayac::find($sayacid);
                            $abonetahsis = AboneTahsis::where('abone_id', $abone->id)->where('abonesayac_id', $abonesayac->id)->first();
                            if ($abonetahsis) {
                                $serviskayit = ServisKayit::where('subekodu',$bilgi->subekodu)->where('abonetahsis_id',$abonetahsis->id)
                                    ->where('tipi',$tipi)->where('durum',0)->first();
                                if($serviskayit){
                                    $serviskayit->delete();
                                    BackendController::HatirlatmaSil(13, $abone->netsiscari_id, 6, 1);
                                    BackendController::BildirimGeriAl(13, $abone->netsiscari_id, 6, 1);
                                }
                            } else {
                                return 2;
                            }
                        }
                    }
                }
            }else{// arıza montaj
                //$aboneteslim = AboneTeslim::find($id);
                $abone = Abone::find($bilgi->abone_id);
                $secilenlist = explode(',',$bilgi->secilenler);
                foreach ($secilenlist as $sayacgelenid){
                    $sayacgelen = SayacGelen::find($sayacgelenid);
                    $abonesayac = AboneSayac::where('serino',$sayacgelen->serino)->first();
                    if(!$abonesayac){
                        return 3;
                    }
                    $abonetahsis = AboneTahsis::where('abone_id', $abone->id)->where('abonesayac_id', $abonesayac->id)->first();
                    if ($abonetahsis) {
                        $sube = Sube::where('subekodu',$abone->subekodu)->where('aktif',1)->first();
                        if(!$sube){
                            return 4;
                        }
                        $serviskayit = ServisKayit::where('subekodu',$sube->subekodu)->where('abonetahsis_id',$abonetahsis->id)
                            ->where('tipi',$tipi)->where('durum',0)->first();
                        if($serviskayit){
                            $serviskayit->delete();
                            BackendController::HatirlatmaSil(13, $abone->netsiscari_id, 6, 1);
                            BackendController::BildirimGeriAl(13, $abone->netsiscari_id, 6, 1);
                        }
                    } else {
                        return 2;
                    }
                }
            }
            return 1;
        } catch (Exception $e) {
            Log::error($e);
            return 0;
        }
    }

    public static function ServisBilgisiDuzenle($bilgi,$tipi,$eskisayaclistesi){
        try {
            $eskisayaclar = explode(';',$eskisayaclistesi);
            if($tipi==0){ //sayaç montaj
                //$sayacsatis = SubeSayacSatis::find($id);
                $abone = Abone::find($bilgi->abone_id);
                $sayaclar = explode(';', $bilgi->sayaclar);
                foreach ($sayaclar as $sayac) {
                    $sayaclist = explode(',', $sayac);
                    $eskisayaclist = explode(',',$eskisayaclar);
                    foreach ($sayaclist as $sayacid) {
                        if ($sayacid != 0) {
                            $abonesayac = AboneSayac::find($sayacid);
                            $abonetahsis = AboneTahsis::where('abone_id', $abone->id)->where('abonesayac_id', $abonesayac->id)->first();
                            if ($abonetahsis) {
                                if (!in_array($sayacid, $eskisayaclist)) {
                                    $serviskayit = new ServisKayit;
                                    $serviskayit->subekodu = $bilgi->subekodu;
                                    $serviskayit->kayitadres = $abonesayac->adres;
                                    $serviskayit->abonetahsis_id = $abonetahsis->id;
                                    $serviskayit->netsiscari_id = $bilgi->netsiscari_id;
                                    $serviskayit->uretimyer_id = $bilgi->uretimyer_id;
                                    $serviskayit->kullanici_id = Auth::user()->id;
                                    $serviskayit->tipi = $tipi;
                                    $serviskayit->aciklama = $abonesayac->bilgi;
                                    $serviskayit->acilmatarihi = $bilgi->faturatarihi;
                                    $serviskayit->save();

                                    BackendController::HatirlatmaEkle(13, $abone->netsiscari_id, 6, 1);
                                    BackendController::BildirimEkle(13, $abone->netsiscari_id, 6, 1);
                                }else{
                                    $serviskayit = ServisKayit::where('subekodu',$bilgi->subekodu)->where('abonetahsis_id',$abonetahsis->id)
                                        ->where('tipi',$tipi)->where('durum',0)->first();
                                    if($serviskayit){
                                        $serviskayit->kayitadres = $abonesayac->adres;
                                        $serviskayit->netsiscari_id = $bilgi->netsiscari_id;
                                        $serviskayit->uretimyer_id = $bilgi->uretimyer_id;
                                        $serviskayit->kullanici_id = Auth::user()->id;
                                        $serviskayit->tipi = $tipi;
                                        $serviskayit->aciklama = $abonesayac->bilgi;
                                        $serviskayit->acilmatarihi = $bilgi->faturatarihi;
                                        $serviskayit->save();
                                    }
                                }
                            } else {
                                return 2;
                            }
                        }
                    }
                    foreach ($eskisayaclist as $eskisayac){
                        if(!in_array($eskisayac,$sayaclist)) {
                            $abonesayac = AboneSayac::find($eskisayac);
                            $abonetahsis = AboneTahsis::where('abone_id', $abone->id)->where('abonesayac_id', $abonesayac->id)->first();
                            if ($abonetahsis) {
                                $serviskayit = ServisKayit::where('subekodu', $bilgi->subekodu)->where('abonetahsis_id', $abonetahsis->id)
                                    ->where('tipi',$tipi)->where('durum',0)->first();
                                if ($serviskayit) {
                                    $serviskayit->delete();
                                    BackendController::HatirlatmaSil(13, $abone->netsiscari_id, 6, 1);
                                    BackendController::BildirimGeriAl(13, $abone->netsiscari_id, 6, 1);
                                }
                            }
                        }
                    }
                }
            }else{// arıza montaj
                //düzenlenecek
                //$aboneteslim = AboneTeslim::find($id);
                $abone = Abone::find($bilgi->abone_id);
                $sayaclar = explode(';', $bilgi->sayaclar);
                foreach ($sayaclar as $sayac) {
                    $sayaclist = explode(',', $sayac);
                    $eskisayaclist = explode(',',$eskisayaclar);
                    foreach ($sayaclist as $sayacid) {
                        if ($sayacid != 0) {
                            $abonesayac = AboneSayac::find($sayacid);
                            $abonetahsis = AboneTahsis::where('abone_id', $abone->id)->where('abonesayac_id', $abonesayac->id)->first();
                            if ($abonetahsis) {
                                if (!in_array($sayacid, $eskisayaclist)) {
                                    $serviskayit = new ServisKayit;
                                    $serviskayit->subekodu = $bilgi->subekodu;
                                    $serviskayit->kayitadres = $abonesayac->adres;
                                    $serviskayit->abonetahsis_id = $abonetahsis->id;
                                    $serviskayit->netsiscari_id = $bilgi->netsiscari_id;
                                    $serviskayit->uretimyer_id = $bilgi->uretimyer_id;
                                    $serviskayit->kullanici_id = Auth::user()->id;
                                    $serviskayit->tipi = $tipi;
                                    $serviskayit->aciklama = $abonesayac->bilgi;
                                    $serviskayit->acilmatarihi = $bilgi->faturatarihi;
                                    $serviskayit->save();

                                    BackendController::HatirlatmaEkle(13, $abone->netsiscari_id, 6, 1);
                                    BackendController::BildirimEkle(13, $abone->netsiscari_id, 6, 1);
                                }else{
                                    $serviskayit = ServisKayit::where('subekodu',$bilgi->subekodu)->where('abonetahsis_id',$abonetahsis->id)
                                        ->where('tipi',$tipi)->where('durum',0)->first();
                                    if($serviskayit){
                                        $serviskayit->kayitadres = $abonesayac->adres;
                                        $serviskayit->netsiscari_id = $bilgi->netsiscari_id;
                                        $serviskayit->uretimyer_id = $bilgi->uretimyer_id;
                                        $serviskayit->kullanici_id = Auth::user()->id;
                                        $serviskayit->tipi = $tipi;
                                        $serviskayit->aciklama = $abonesayac->bilgi;
                                        $serviskayit->acilmatarihi = $bilgi->faturatarihi;
                                        $serviskayit->save();
                                    }
                                }
                            } else {
                                return 2;
                            }
                        }
                    }
                    foreach ($eskisayaclist as $eskisayac){
                        if(!in_array($eskisayac,$sayaclist)) {
                            $abonesayac = AboneSayac::find($eskisayac);
                            $abonetahsis = AboneTahsis::where('abone_id', $abone->id)->where('abonesayac_id', $abonesayac->id)->first();
                            if ($abonetahsis) {
                                $serviskayit = ServisKayit::where('subekodu', $bilgi->subekodu)->where('abonetahsis_id', $abonetahsis->id)
                                    ->where('tipi',$tipi)->where('durum',0)->first();
                                if ($serviskayit) {
                                    $serviskayit->delete();
                                    BackendController::HatirlatmaSil(13, $abone->netsiscari_id, 6, 1);
                                    BackendController::BildirimGeriAl(13, $abone->netsiscari_id, 6, 1);
                                }
                            }
                        }
                    }
                }
            }
            return 1;
        } catch (Exception $e) {
            Log::error($e);
            return 0;
        }
    }

    public static function SatisFaturasiDuzenle($id,$eskibilgi){ // Netsis Tarafında Satış Faturasını Karşılar
        try {
            $sabit = SistemSabitleri::where('adi', 'NetsisFatura')->first();
            if ($sabit->deger == 0) //fatura kesilmeyecek
            {
                return 0;
            } else {
                $sayacsatis = SubeSayacSatis::find($id);
                $sube = Sube::where('subekodu',$sayacsatis->subekodu)->where('aktif',1)->first();
                $subeyetkili = SubeYetkili::where('kullanici_id', Auth::user()->id)->where('aktif', 1)->first();
                $netsiscari = NetsisCari::find($sayacsatis->netsiscari_id);
                $vadegunu = is_null($netsiscari->vadegunu) ? 0 : $netsiscari->vadegunu;
                $odemetarih = date('Y-m-d', strtotime($sayacsatis->faturatarihi . ' + ' . $vadegunu . ' days'));
                $abone = Abone::find($sayacsatis->abone_id);
                $aboneil = Iller::find($abone->iller_id);
                $aboneilce = Ilceler::find($abone->ilceler_id);
                $fatirsno = $sayacsatis->faturano;
                $parabirimi = ParaBirimi::find($sayacsatis->parabirimi_id);
                $secilenler = explode(',', $sayacsatis->secilenler);
                $sayaclar = explode(';', $sayacsatis->sayaclar);
                $adetler = explode(',', $sayacsatis->adet);
                $birimfiyatlar = explode(',', $sayacsatis->birimfiyat);
                $ucretsizler = explode(',', $sayacsatis->ucretsiz);
                $urunler = array();
                $status = 0;
                for ($i = 0; $i < count($secilenler); $i++) {
                    $urun = SubeUrun::find($secilenler[$i]);
                    $urun->adet = $adetler[$i];
                    $urun->fiyat = $birimfiyatlar[$i];
                    $urun->ucretsiz = $ucretsizler[$i];
                    $urun->stokkodu = NetsisStokKod::find($urun->netsisstokkod_id);
                    if ($urun->baglanti) {
                        $sayaclist = explode(',', $sayaclar[$i]);
                        $urun->sayaclar = AboneSayac::where('sayacadi_id', $urun->sayacadi_id)->where('sayaccap_id', $urun->sayaccap_id)
                            ->where('uretimyer_id', $sayacsatis->uretimyer_id)->whereIn('id', $sayaclist)->get();
                    } else {
                        $urun->sayaclar = array();
                    }
                    array_push($urunler, $urun);
                }
                try {
                    if($sayacsatis->odemesekli=="NAKİT+KREDİ KARTI"){
                        $status = 1;
                        $kasakod = KasaKod::where('subekodu', $subeyetkili->subekodu)->where('kasakod', $sayacsatis->kasakodu)->first();
                        $sayacsatis->odemesekli = 'NAKİT:'.floatval($sayacsatis->odeme).' '.$parabirimi->yazi.' + K.KARTI('.$kasakod->kisaadi.'):'.floatval($sayacsatis->odeme2).' '.$parabirimi->yazi;
                    }else if($sayacsatis->odemesekli=="2 KREDİ KARTI"){
                        $status = 1;
                        $kasakod = KasaKod::where('subekodu', $subeyetkili->subekodu)->where('kasakod', $sayacsatis->kasakodu)->first();
                        $kasakod2 = KasaKod::where('subekodu', $subeyetkili->subekodu)->where('kasakod', $sayacsatis->kasakodu2)->first();
                        $sayacsatis->odemesekli = 'K.KARTI('.$kasakod->kisaadi.'):'.floatval($sayacsatis->odeme).' '.$parabirimi->yazi.' + K.KARTI('.$kasakod2->kisaadi.'):'.floatval($sayacsatis->odeme2).' '.$parabirimi->yazi;
                    }else if($sayacsatis->odemesekli=="KREDİ KARTI"){
                        $kasakod = KasaKod::where('subekodu', $subeyetkili->subekodu)->where('kasakod', $sayacsatis->kasakodu)->first();
                        $sayacsatis->odemesekli = $sayacsatis->odemesekli.'('.$kasakod->kisaadi.')';
                    }else if($sayacsatis->odemesekli=="BANKA HAVALESİ"){
                        $status  = 1;
                    }else if($sayacsatis->odemesekli=="BELLİ DEĞİL"){
                        $status  = 1;
                    }
                    $sayacsatis->save();
                    $durum = BackendController::SatisFaturasiSil($eskibilgi);
                    if ($durum != 1) {
                        return $durum;
                    }
                    $fatura = new Fatuirs;
                    $faturaek = new Fatuek;
                    $fatura->SUBE_KODU = $sayacsatis->subekodu;
                    $fatura->FATIRS_NO = $fatirsno;
                    $fatura->FTIRSIP = '1';
                    $fatura->CARI_KODU = BackendController::ReverseTrk($sayacsatis->carikod);
                    $fatura->TARIH = $sayacsatis->faturatarihi;
                    $fatura->TIPI = $status ? 2 : 1; //1 Kapalı Fatura 2 Açık Fatura
                    $fatura->BRUTTUTAR = $sayacsatis->tutar;
                    $fatura->KDV = $sayacsatis->kdv;
                    $fatura->ACIKLAMA = BackendController::ReverseTrk(mb_substr(trim($abone->adisoyadi),0,
                        (strlen(utf8_decode(trim($abone->adisoyadi)))>20 ? 20 : strlen(utf8_decode(trim($abone->adisoyadi)))),'utf-8'));
                    $fatura->KOD1 = '0';
                    $fatura->ODEMEGUNU = $vadegunu;
                    $fatura->ODEMETARIHI = $odemetarih;
                    $fatura->KDV_DAHILMI = 'E';
                    $fatura->FATKALEM_ADEDI = count($urunler);
                    $fatura->SIPARIS_TEST = $sayacsatis->faturatarihi;
                    $fatura->GENELTOPLAM = $sayacsatis->toplamtutar;
                    $fatura->PLA_KODU = $sayacsatis->plasiyerkod;
                    $fatura->KS_KODU = $sayacsatis->kasakodu;
                    $fatura->C_YEDEK6 = 'X';
                    $fatura->D_YEDEK10 = $sayacsatis->faturatarihi;
                    $fatura->PROJE_KODU = $sayacsatis->projekodu;
                    if($status) $fatura->FIYATTARIHI = $sayacsatis->faturatarihi;
                    $fatura->KAYITYAPANKUL = $sayacsatis->netsiskullanici;
                    $fatura->KAYITTARIHI = date('Y-m-d H:i:s');
                    $fatura->DUZELTMEYAPANKUL = $sayacsatis->netsiskullanici;
                    $fatura->DUZELTMETARIHI = date('Y-m-d H:i:s');
                    $fatura->GELSUBE_KODU = 0;
                    $fatura->GITSUBE_KODU = 0;
                    $fatura->ISLETME_KODU = 1;
                    $fatura->KOSVADEGUNU = 0;
                    $fatura->GIB_FATIRS_NO = BackendController::GibFaturaNo($fatirsno,BackendController::FaturaOnEk($sayacsatis));
                    $fatura->SIST_EARSIVMI = NULL;
                    $fatura->EBELGE = 0;
                    $fatura->save();
                } catch (Exception $e) {
                    Log::error($e);
                    return 2;
                }
                try {
                    $faturaek->SUBE_KODU = $sayacsatis->subekodu;
                    $faturaek->FKOD = '1';
                    $faturaek->FATIRSNO = $fatirsno;
                    $faturaek->CKOD = BackendController::ReverseTrk($sayacsatis->carikod);
                    if($sayacsatis->netsiscari_id==$sube->netsiscari_id){
                        if($abone->vergidairesi!=""){
                            $faturaek->ACIK1 = $abone->tckimlikno;
                            $faturaek->ACIK6 = BackendController::reverseTrk($abone->vergidairesi);
                        }else{
                            $faturaek->ACIK1 = $abone->tckimlikno;
                        }
                        $faturaek->ACIK3 = BackendController::reverseTrk($abone->adisoyadi);
                        $faturaek->ACIK4 = BackendController::reverseTrk($abone->adisoyadi);
                        $faturaek->ACIK5 = BackendController::reverseTrk($abone->adisoyadi);
                    }else{
                        $faturaek->ACIK1 = $netsiscari->vergino;
                        $faturaek->ACIK6 = BackendController::reverseTrk($netsiscari->vergidairesi);
                        $faturaek->ACIK3 = BackendController::reverseTrk($netsiscari->cariadi);
                    }
                    $faturaek->ACIK7 = BackendController::reverseTrk($abone->faturaadresi);
                    $faturaek->ACIK8 = BackendController::reverseTrk($aboneil->adi);
                    $faturaek->ACIK9 = BackendController::reverseTrk($aboneilce->adi);
                    $faturaek->ACIK10 = $abone->email=="" ? null : $abone->email;
                    $faturaek->ACIK11 = $abone->telefon;
                    $faturaek->ACIK14 = BackendController::reverseTrk($sayacsatis->aciklama);
                    $faturaek->ACIK15 = BackendController::reverseTrk($sayacsatis->odemesekli=="BELLİ DEĞİL" ? "" : $sayacsatis->odemesekli);
                    $faturaek->ACIK16 = BackendController::reverseTrk($abone->aciklama);
                    $faturaek->save();
                } catch (Exception $e) {
                    Log::error($e);
                    return 3;
                }
                try {
                    $inckey = array();
                    for ($i = 0; $i < count($urunler); $i++) {
                        $faturakalem = new Sthar;
                        $faturakalem->STOK_KODU = $urunler[$i]->stokkodu->kodu;
                        $faturakalem->FISNO = $fatirsno;
                        $faturakalem->STHAR_GCMIK = $urunler[$i]->adet;
                        $faturakalem->STHAR_GCKOD = 'C';
                        $faturakalem->STHAR_TARIH = $sayacsatis->faturatarihi;
                        $kalemtutar = $urunler[$i]->fiyat;
                        $kalembrut = BackendController::TruncateNumber((($kalemtutar*100)/118),8);
                        $faturakalem->STHAR_NF = $kalembrut;
                        $faturakalem->STHAR_BF = $kalemtutar;
                        $faturakalem->STHAR_DOVTIP = 0;
                        $faturakalem->STHAR_DOVFIAT = 0;
                        $faturakalem->STHAR_KDV = 18;
                        $faturakalem->DEPO_KODU = $urunler[$i]->depokodu;
                        $faturakalem->STHAR_ACIKLAMA = BackendController::ReverseTrk($sayacsatis->carikod);
                        $faturakalem->STHAR_FTIRSIP = '1';
                        $faturakalem->LISTE_FIAT = 1;
                        $faturakalem->STHAR_HTUR = 'I';
                        $faturakalem->STHAR_ODEGUN = $vadegunu;
                        $faturakalem->STHAR_BGTIP = 'F';
                        $faturakalem->STHAR_KOD1 = '0';
                        $faturakalem->STHAR_CARIKOD = BackendController::ReverseTrk($sayacsatis->carikod);
                        $faturakalem->PLASIYER_KODU = $sayacsatis->plasiyerkod;
                        $faturakalem->EKALAN_NEDEN = 1;
                        $faturakalem->EKALAN = BackendController::ReverseTrk($urunler[$i]->urunadi);
                        $faturakalem->SIRA = $i + 1;
                        $faturakalem->STRA_SIPKONT = 0;
                        $faturakalem->OLCUBR = 1;
                        $faturakalem->VADE_TARIHI = $odemetarih;
                        $faturakalem->SUBE_KODU = $sayacsatis->subekodu;
                        $faturakalem->D_YEDEK10 = $sayacsatis->faturatarihi;
                        $faturakalem->PROJE_KODU = $sayacsatis->projekodu;
                        $faturakalem->DUZELTMETARIHI = date('Y-m-d H:i:s');
                        if($status) $faturakalem->FIYATTARIHI = date('Y-m-d');
                        $faturakalem->save();
                        $faturainckey = DB::connection('sqlsrv2')->select("SELECT TOP(1)INCKEYNO FROM tblsthar WHERE SUBE_KODU='".$sayacsatis->subekodu."' and FISNO='" . $fatirsno . "' and STHAR_FTIRSIP='1' ORDER BY INCKEYNO DESC");
                        $inckeyno = ($faturainckey[0]->INCKEYNO);
                        if (in_array($inckeyno, $inckey)) { //girilen inckey zaten ekliyse kaydedememiş demektir
                            return 7;
                        } else { //kaydetme başarılı
                            array_push($inckey, $inckeyno);
                        }
                    }
                } catch (Exception $e) {
                    Log::error($e);
                    return 4;
                }

                $durum = BackendController::SeriKaydet($sayacsatis, $inckey);
                if ($durum != 1) {
                    return 10;
                }
                if(!$status){ //AÇIK FATURA KASA KAYIDI YAPILMIYOR
                    $durum = BackendController::KasaKaydet($sayacsatis, 1);
                    if ($durum != 1) {
                        return 6;
                    }
                }

                $durum = BackendController::MuhasebeFisKaydet($sayacsatis, $status);
                if ($durum != 1) {
                    return 11;
                }

                $durum = BackendController::CariHareketKaydet($sayacsatis, $status);
                if ($durum != 1) {
                    return 12;
                }

                $durum = BackendController::ServisBilgisiEkle($sayacsatis, 0); // Servis Bilgisi Eklenecek 0 Satış 1 Tamir sonrası servis 2 arıza kontrolü
                if ($durum == 0) {
                    if(!$status) //AÇIK FATURA KASA KAYIDI YAPILMIYOR
                        Kasa::where('FISNO', $fatirsno)->update(array('GECERLI'=>'H'));
                    return 8;
                } else if ($durum == 2) {
                    if(!$status) //AÇIK FATURA KASA KAYIDI YAPILMIYOR
                    Kasa::where('FISNO', $fatirsno)->update(array('GECERLI'=>'H'));
                    return 9;
                }else{
                    return 1;
                }
            }
        } catch (Exception $e) {
            Log::error($e);
            return str_replace("'","\'",$e->getMessage());
        }
    }

    public static function BarkodInc($uretimbarkod){
	    $barkodsub=$uretimbarkod->barkod;
	    $barkod=array("0","0","0");
	    $inc=[0,0,1];
	    $subarray=str_split($barkodsub,1);
	    for($i=count($subarray)-1;$i>=0;$i--){
	        if($inc[$i]==1){
                if($subarray[$i]=='Z'){
                    $barkod[$i]='0';
                    if($i==0) return false;
                    $inc[$i-1]=1;
                }else if ($subarray[$i]=='9'){
                    $barkod[$i]='A';
                }else{
                    $barkod[$i]=++$subarray[$i];
                }
            }else{
	            $barkod[$i]=$subarray[$i];
            }
        }
	    $barkod=implode("",$barkod);
	    $uretimbarkod->barkod=$barkod;
	    $uretimbarkod->save();
	    return $barkod;
    }

    public static function BarkodOlustur($uretimurun,$olduretimurun=false){
	    $stokkodu = NetsisStokKod::find($uretimurun->netsisstokkod_id);
	    $netsiscari = NetsisCari::find($uretimurun->netsiscari_id);
	    if($netsiscari){
            $carikod = explode("-",$netsiscari->carikod);
        }else{
	        $carikod = array("00","00","M000");
        }
	    $uretimbarkod = UretimUrunBarkod::where('netsisstokkod_id',$uretimurun->netsisstokkod_id)->where('netsiscari_id',$uretimurun->netsiscari_id)->first();
	    if(!$uretimbarkod){
            $uretimbarkod=new UretimUrunBarkod;
            $uretimbarkod->netsisstokkod_id=$uretimurun->netsisstokkod_id;
            $uretimbarkod->netsiscari_id=$uretimurun->netsiscari_id;
            $uretimbarkod->barkod="000";
            $uretimbarkod->save();
        }
        if($olduretimurun && $olduretimurun->netsiscari_id==$uretimurun->netsiscari_id && $olduretimurun->netsisstokkod_id==$uretimurun->netsisstokkod_id){
            return $olduretimurun->barkod;
        }
        $barkodsub=BackendController::BarkodInc($uretimbarkod);
        if($barkodsub){
            return substr($stokkodu->kodu,0,1).substr($stokkodu->kodu,-4).$carikod[1].$carikod[2].$barkodsub;
        }
        return false;
    }

    public static function ReceteBilgisi($stokkodu){
        $recete = UretimRecete::where('MAMUL_KODU',$stokkodu)->where('OPR_BIL','B')->orderBy('OPNO','asc')
            ->get(array(DB::raw('dbo.TRK(MAMUL_KODU) as MAMUL_KODU'),DB::raw('dbo.TRK(MAMUL_ADI) as MAMUL_ADI'),
                DB::raw('dbo.TRK(HAM_KODU) as HAM_KODU'),DB::raw('dbo.TRK(HAMMADDE_ADI) as HAMMADDE_ADI'),'MIKTAR'));
        if($recete->count()>0){
            foreach ($recete as $kalem){
                $kalem->MIKTAR = intval($kalem->MIKTAR);
                $kalem->kalem=self::ReceteBilgisi($kalem->HAM_KODU);
                if($kalem->kalem->count()>0){
                    $kalem->barkodlar = UretimSonuUrun::where('netsisstokkod.kodu',$kalem->HAM_KODU)->whereNull('uretimsonuurun.uretimsonuurun_id')
                        ->leftJoin('netsisstokkod','netsisstokkod.id',"=",'uretimsonuurun.netsisstokkod_id')
                        ->get(array('uretimsonuurun.serino as barkod','uretimsonuurun.id'))->toArray();
                }else{
                    $kalem->barkodlar = UretimUrun::where('netsisstokkod.kodu',$kalem->HAM_KODU)->where('kalan','>',0)
                        ->leftJoin('netsisstokkod','netsisstokkod.id',"=",'uretimurun.netsisstokkod_id')
                        ->get(array('uretimurun.barkod','uretimurun.id','uretimurun.adet','uretimurun.kullanilan','uretimurun.kalan'))->toArray();
                    $kalem->muadiller = UretimUrun::where('netsisstokkod.kodu',$kalem->HAM_KODU)->where('kalan','>',0)
                        ->leftJoin('netsisstokkod','netsisstokkod.id',"=",'uretimurun.muadil')
                        ->get(array('uretimurun.barkod','uretimurun.id','uretimurun.adet','uretimurun.kullanilan','uretimurun.kalan'))->toArray();
                }
            }
        }
        return $recete;
    }

    public static function ReceteUrunBilgisi($stokkodu){
        $recete = UretimRecete::where('HAM_KODU',$stokkodu)->where('OPR_BIL','B')->orderBy('OPNO','asc')
            ->get(array(DB::raw('dbo.TRK(MAMUL_KODU) as MAMUL_KODU'),DB::raw('dbo.TRK(MAMUL_ADI) as MAMUL_ADI'),
                DB::raw('dbo.TRK(HAM_KODU) as HAM_KODU'),DB::raw('dbo.TRK(HAMMADDE_ADI) as HAMMADDE_ADI'),'MIKTAR'));
        if($recete->count()>0){
            foreach ($recete as $kalem){
                $kalem->MIKTAR = intval($kalem->MIKTAR);
            }
        }
        return $recete;
    }

    public static function UretimSonuKullanilanGrupla($kullanilanlist){
        $netsisstokkodu=$uretimurun=$urunadet=$depokodu=array();
        foreach ($kullanilanlist as $key => $row) {
            $netsisstokkodu[$key] = $row['netsisstokkod_id'];
            $uretimurun[$key] = $row['uretimurun_id'];
            $urunadet[$key] = $row['urunadet'];
            $depokodu[$key] = $row['depokodu'];
        }
        array_multisort($netsisstokkodu, SORT_ASC,$depokodu, SORT_ASC, $kullanilanlist);
        $i=0;
        $list=array();
        foreach($kullanilanlist as $kullanilan)
        {
            if($i==0)
            {
                array_push($list, array('kod' => $kullanilan['netsisstokkod_id'], 'urun' => array($kullanilan), 'adet' => $kullanilan['urunadet'],'depokodu'=>$kullanilan['depokodu']));
                $i++;
            }else{
                if($list[$i-1]['kod']==$kullanilan['netsisstokkod_id']){
                    if($list[$i-1]['depokodu']==$kullanilan['depokodu']){
                        $list[$i - 1]['adet'] += $kullanilan['urunadet'];
                        array_push($list[$i - 1]['urun'], $kullanilan);
                    }else{
                        array_push($list, array('kod' => $kullanilan['netsisstokkod_id'], 'urun' => array($kullanilan), 'adet' => $kullanilan['urunadet'],'depokodu'=>$kullanilan['depokodu']));
                        $i++;
                    }
                }else{
                    array_push($list, array('kod' => $kullanilan['netsisstokkod_id'], 'urun' => array($kullanilan), 'adet' => $kullanilan['urunadet'],'depokodu'=>$kullanilan['depokodu']));
                    $i++;
                }
            }
        }
        return $list;
    }

    public static function UretimSonuKayitEkle($uretimsonukayitid){
        try {
            $uretimsonukayit = UretimSonu::find($uretimsonukayitid);
            $netsisstokkod = NetsisStokKod::find($uretimsonukayit->netsisstokkod_id);
            $isemri = IsEmri::find(BackendController::ReverseTrk($uretimsonukayit->isemrino));
            $servisyetkili = ServisYetkili::where('kullanici_id', Auth::user()->id)->first();
            if (!$servisyetkili) {
                return array('durum' => '0', 'text' => 'Üretim Sonu Kayıdı Ekleme Yetkiniz Yok');
            }
            try {
                $stokurs = new StokUrs;
                $stokurs->URETSON_FISNO = BackendController::UretimSonuFisNo();
                $stokurs->URETSON_TARIH = date('Y-m-d');
                $stokurs->URETSON_SIPNO = BackendController::ReverseTrk($uretimsonukayit->isemrino);
                $stokurs->URETSON_DEPO = $uretimsonukayit->girisdepo;
                $stokurs->URETSON_MAMUL = BackendController::ReverseTrk($netsisstokkod->kodu);
                $stokurs->URETSON_MIKTAR = $uretimsonukayit->adet;
                $stokurs->URETSON_MALY1 = 0.00;
                $stokurs->URETSON_MALY2 = 0.00;
                $stokurs->SUBE_KODU = 1;
                $stokurs->I_YEDEK1 = $uretimsonukayit->cikisdepo;
                $stokurs->I_YEDEK2 = 0;
                $stokurs->PROJE_KODU = 1;
                $stokurs->ACIKLAMA = $isemri->ACIKLAMA;
                $stokurs->RECETE_TARIHI = date('Y-m-d');
                $stokurs->ONCELIK = 0;
                $stokurs->KAYITYAPANKUL = $servisyetkili->netsiskullanici;
                $stokurs->KAYITTARIHI = date('Y-m-d H:i:s');
                $stokurs->KAYIT_EDILSIN = 1;
                $stokurs->BELGE_TIPI = 'B';
                $stokurs->save();
                $stokursek = new StokUrsEk;
                $stokursek->INCKEYNOEK = $stokurs->INCKEYNO;
                $stokursek->save();
                $uretimsonukayit->inckeyno = $stokurs->INCKEYNO;
                $uretimsonukayit->save();
            } catch (Exception $e) {
                Log::error($e);
                return array('durum' => '0', 'text' => 'Üretim Sonu Kayıdı Netsis Tarafına Kaydedilemedi');
            }

            $urunler = UretimSonuUrun::where('uretimsonu_id', $uretimsonukayitid)->get();
            $eklenenler = array();
            foreach ($urunler as $urun) {
                try {
                    $urunstokkod = NetsisStokKod::find($urun->netsisstokkod_id);
                    if(Seritra::where('KAYIT_TIPI','A')->where('STOK_KODU',BackendController::ReverseTrk($urunstokkod->kodu))->where('SERI_NO',$urun->serino)
                        ->where('SUBE_KODU',$stokurs->SUBE_KODU)->where('BELGETIP','C')->first()){
                        return array('durum' => '0', 'text' => $urun->serino.' Seri Numarasına Ait Bu Stok Kodunda Ürün Mevcut!');
                    }
                    $seritra = new Seritra;
                    $seritra->KAYIT_TIPI = 'A';
                    $seritra->SERI_NO = $urun->serino;
                    $seritra->STOK_KODU = BackendController::ReverseTrk($urunstokkod->kodu);
                    $seritra->STRA_INC = $stokurs->INCKEYNO;
                    $seritra->TARIH = $stokurs->URETSON_TARIH;
                    $seritra->ACIK1 = '';
                    $seritra->ACIK2 = '';
                    $seritra->GCKOD = 'G';
                    $seritra->MIKTAR = 1;
                    $seritra->MIKTAR2 = 0;
                    $seritra->BELGENO = $stokurs->URETSON_FISNO;
                    $seritra->BELGETIP = 'C';
                    $seritra->HARACIK = 'Uretim';
                    $seritra->SUBE_KODU = $stokurs->SUBE_KODU;
                    $seritra->DEPOKOD = $stokurs->URETSON_DEPO;
                    $seritra->SIPNO = $stokurs->URETSON_SIPNO;
                    $seritra->KARSISERI = '';
                    $seritra->YEDEK1 = '';
                    $seritra->YEDEK4 = 0;
                    $seritra->ONAYTIPI = 'A';
                    $seritra->ONAYNUM = 0;
                    $seritra->ACIK3 = '';
                    $seritra->save();
                    $sira = DB::connection('sqlsrv2')->select("select top(1)SIRA_NO from tblseritra where SUBE_KODU=" . $stokurs->SUBE_KODU . " and STRA_INC=" . $stokurs->INCKEYNO . " order by SIRA_NO desc");
                    $sirano = ($sira[0]->SIRA_NO);
                    if (in_array($sirano, $eklenenler)) { //gelen sırano zaten ekliyse kaydedememiş demektir
                        return array('durum' => '0', 'text' => 'Üretilen Ürünün Seri Numarası Netsis Tarafına Kaydedilemedi');
                    } else { //kaydetme başarılı
                        array_push($eklenenler, $sirano);
                        $urun->sirano = $sirano;
                        $urun->save();
                    }
                } catch (Exception $e) {
                    Log::error($e);
                    return array('durum' => '0', 'text' => 'Üretilen Ürünlerin Seri Numarası Netsis Tarafına Kaydedilirken Hata Oluştu');
                }
            }

            $kullanilanlist = UretimSonuKullanilan::where('uretimsonu_id',$uretimsonukayitid)->get()->toArray();
            $uretimsonukullanilanlar = BackendController::UretimSonuKullanilanGrupla($kullanilanlist);
            $kullanilanlar = array();
            $toplamharcama = 0;
            try {
                foreach ($uretimsonukullanilanlar as $urun) {
                    $urunstokkod = NetsisStokKod::find($urun['kod']);
                    $sthar = new Sthar;
                    $sthar->STOK_KODU = BackendController::ReverseTrk($urunstokkod->kodu);
                    $sthar->FISNO = $stokurs->URETSON_FISNO;
                    $sthar->STHAR_GCMIK = $urun['adet'];
                    $sthar->STHAR_GCKOD = 'C';
                    $sthar->STHAR_TARIH = $stokurs->URETSON_TARIH;
                    $sthar->STHAR_DOVTIP = 0;
                    $sthar->STHAR_DOVFIAT = 0;
                    if(count($urun['urun'])>1){
                        $urunharcama = 0;
                        foreach ($urun['urun'] as $kullanilan){
                            if(is_null($kullanilan['uretimurun_id'])){ //kullanılan alt ürün ise
                                $kullanilan['uretimsonuurun'] = UretimSonuUrun::where('uretimsonu_id',$kullanilan['uretimsonu_id'])->first();
                                $kullanilan['uretimsonualturun'] = UretimSonuUrun::where('uretimsonuurun_id',$kullanilan['uretimsonuurun']->id)->first();
                                $urunharcama += ($kullanilan['urunadet']*$kullanilan['uretimsonualturun']->birimfiyat);
                            }else{
                                $kullanilan['uretimurun'] = UretimUrun::find($kullanilan['uretimurun_id']);
                                $urunharcama += ($kullanilan['urunadet']*$kullanilan['uretimurun']->birimfiyat);
                            }
                        }
                        $birimfiyat = $urunharcama/$urun['adet'];
                    }else{
                        if(is_null($urun['urun'][0]['uretimurun_id'])){ //kullanılan alt ürün ise
                            $urun['urun'][0]['uretimsonuurun'] = UretimSonuUrun::where('uretimsonu_id',$urun['urun'][0]['uretimsonu_id'])->first();
                            $urun['urun'][0]['uretimsonualturun'] = UretimSonuUrun::where('uretimsonuurun_id',$urun['urun'][0]['uretimsonuurun']->id)->first();
                            $birimfiyat = $urun['urun'][0]['uretimsonualturun']->birimfiyat;
                        }else {
                            $uretimurun = UretimUrun::find($urun['urun'][0]['uretimurun_id']);
                            $birimfiyat = $uretimurun->birimfiyat;
                        }
                    }
                    $sthar->STHAR_NF = $birimfiyat;
                    $sthar->STHAR_BF = $birimfiyat;
                    $toplamharcama += ($sthar->STHAR_GCMIK*$sthar->STHAR_NF);

                    $sthar->STHAR_KDV = 0;
                    $sthar->DEPO_KODU = $urun['depokodu'];
                    $sthar->STHAR_ACIKLAMA = BackendController::ReverseTrk($stokurs->URETSON_MAMUL);
                    $sthar->STHAR_FTIRSIP = null;
                    $sthar->LISTE_FIAT = 0;
                    $sthar->STHAR_HTUR = 'C';
                    $sthar->STHAR_ODEGUN = null;
                    $sthar->STHAR_BGTIP = 'V';
                    $sthar->STHAR_KOD1 = null;
                    $sthar->STHAR_SIPNUM = $stokurs->URETSON_SIPNO;
                    $sthar->STHAR_CARIKOD = null;
                    $sthar->PLASIYER_KODU = null;
                    $sthar->SIRA = 0;
                    $sthar->STRA_SIPKONT = 0;
                    $sthar->OLCUBR = 0;
                    $sthar->VADE_TARIHI = null;
                    $sthar->SUBE_KODU = $stokurs->SUBE_KODU;
                    $sthar->D_YEDEK10 = null;
                    $sthar->PROJE_KODU = 1;
                    $sthar->save();
                    $id = DB::connection('sqlsrv2')->select("select top(1)INCKEYNO from tblsthar where SUBE_KODU=" . $stokurs->SUBE_KODU . " and ".($stokurs->URETSON_FISNO==null ? "FISNO is null" : "FISNO='" . $stokurs->URETSON_FISNO."'")." and STHAR_FTIRSIP is null order by INCKEYNO desc");
                    $inckeyno = ($id[0]->INCKEYNO);
                    if (in_array($inckeyno, $kullanilanlar)) { //girilen inckey zaten ekliyse kaydedememiş demektir
                        return array('durum' => '0', 'text' => 'Kullanılan Malzemenin Stok Hareketi Netsis Tarafına Kaydedilemedi');
                    } else { //kaydetme başarılı
                        array_push($kullanilanlar, $inckeyno);
                        if(count($urun['urun'])>1){
                            foreach ($urun['urun'] as $kullanilan){
                                UretimSonuKullanilan::find($kullanilan['id'])->update(['inckeyno'=>$inckeyno]);
                            }
                        }else{
                            UretimSonuKullanilan::find($urun['urun'][0]['id'])->update(['inckeyno'=>$inckeyno]);
                        }
                    }
                }
            } catch (Exception $e) {
                Log::error($e);
                return array('durum' => '0', 'text' => 'Kullanılan Ürünlerin Stok Hareketi Netsis Tarafına Kaydedilirken Hata Oluştu');
            }

            $uretilen = array();
            try {
                $sthar = new Sthar;
                $sthar->STOK_KODU = $stokurs->URETSON_MAMUL;
                $sthar->FISNO = $stokurs->URETSON_FISNO;
                $sthar->STHAR_GCMIK = $stokurs->URETSON_MIKTAR;
                $sthar->STHAR_GCKOD = 'G';
                $sthar->STHAR_TARIH = $stokurs->URETSON_TARIH;

                $birimfiyat = $toplamharcama/$stokurs->URETSON_MIKTAR;
                $sthar->STHAR_NF = $birimfiyat;
                $sthar->STHAR_BF = $birimfiyat;
                $sthar->STHAR_DOVTIP = 0;
                $sthar->STHAR_DOVFIAT = 0;

                $sthar->STHAR_KDV = 0;
                $sthar->DEPO_KODU = $uretimsonukayit->girisdepo;
                $sthar->STHAR_ACIKLAMA = BackendController::ReverseTrk('Uretim');
                $sthar->STHAR_FTIRSIP = null;
                $sthar->LISTE_FIAT = 0;
                $sthar->STHAR_HTUR = 'C';
                $sthar->STHAR_ODEGUN = null;
                $sthar->STHAR_BGTIP = 'U';
                $sthar->STHAR_KOD1 = null;
                $sthar->STHAR_SIPNUM = $stokurs->URETSON_SIPNO;
                $sthar->STHAR_CARIKOD = null;
                $sthar->PLASIYER_KODU = null;
                $sthar->SIRA = 0;
                $sthar->STRA_SIPKONT = 0;
                $sthar->OLCUBR = 0;
                $sthar->VADE_TARIHI = null;
                $sthar->SUBE_KODU = $stokurs->SUBE_KODU;
                $sthar->D_YEDEK10 = null;
                $sthar->PROJE_KODU = 1;
                $sthar->save();
                $id = DB::connection('sqlsrv2')->select("select top(1)INCKEYNO from tblsthar where SUBE_KODU=" . $stokurs->SUBE_KODU . " and ".($stokurs->URETSON_FISNO==null ? "FISNO is null" : "FISNO='" . $stokurs->URETSON_FISNO."'")." and STHAR_FTIRSIP is null order by INCKEYNO desc");
                $inckeyno = ($id[0]->INCKEYNO);
                if (in_array($inckeyno, $uretilen)) { //girilen inckey zaten ekliyse kaydedememiş demektir
                    return array('durum' => '0', 'text' => 'Kullanılan Malzemenin Stok Hareketi Netsis Tarafına Kaydedilemedi');
                } else { //kaydetme başarılı
                    array_push($uretilen, $inckeyno);
                    foreach ($urunler as $urun) {
                        $urun->inckeyno = $inckeyno;
                        $urun->birimfiyat = $birimfiyat;
                        $urun->save();
                    }
                }
            } catch (Exception $e) {
                Log::error($e);
                return array('durum' => '0', 'text' => 'Kullanılan Ürünlerin Stok Hareketi Netsis Tarafına Kaydedilirken Hata Oluştu');
            }

            if(!$uretimsonukayit->adet>=$isemri->MIKTAR){
                $isemri->KAPALI='E';
                $isemri->save();
            }
            return array('durum' => '1', 'id' => $stokurs->INCKEYNO);

        } catch (Exception $e) {
            Log::error($e);
            return array('durum'=>'0','text'=>'Üretim Sonu Kayıdı Tamamlanamadı');
        }
    }

    public static function UretimSonuFisNo(){
        $stokurs=StokUrs::orderBy('INCKEYNO','desc')->first();
        if($stokurs){
            return BackendController::FaturaNo($stokurs->URETSON_FISNO,1);
        }else{
            return '000000000000001';
        }
    }

    public static function UretimSonuKayitDuzenle($uretimsonukayitid){
        try {
            $uretimsonukayit = UretimSonu::find($uretimsonukayitid);
            $netsisstokkod = NetsisStokKod::find($uretimsonukayit->netsisstokkod_id);
            $isemri = IsEmri::find(BackendController::ReverseTrk($uretimsonukayit->isemrino));
            $servisyetkili = ServisYetkili::where('kullanici_id', Auth::user()->id)->first();
            if (!$servisyetkili) {
                return array('durum' => '0', 'text' => 'Üretim Sonu Kayıdı Düzenleme Yetkiniz Yok');
            }
            try {
                $stokurs = StokUrs::find($uretimsonukayit->inckeyno);
                if(!$stokurs){
                    return array('durum' => '0', 'text' => 'Düzenleme Yapılacak Üretim Sonu Kayıdı Bulunamadı');
                }
                $stokurs->URETSON_TARIH = date('Y-m-d');
                $stokurs->URETSON_SIPNO = BackendController::ReverseTrk($uretimsonukayit->isemrino);
                $stokurs->URETSON_DEPO = $uretimsonukayit->girisdepo;
                $stokurs->URETSON_MAMUL = BackendController::ReverseTrk($netsisstokkod->kodu);
                $stokurs->URETSON_MIKTAR = $uretimsonukayit->adet;
                $stokurs->URETSON_MALY1 = 0.00;
                $stokurs->URETSON_MALY2 = 0.00;
                $stokurs->SUBE_KODU = 1;
                $stokurs->I_YEDEK1 = $uretimsonukayit->cikisdepo;
                $stokurs->I_YEDEK2 = 0;
                $stokurs->PROJE_KODU = 1;
                $stokurs->ACIKLAMA = $isemri->ACIKLAMA;
                $stokurs->RECETE_TARIHI = date('Y-m-d');
                $stokurs->ONCELIK = 0;
                $stokurs->DUZELTMEYAPANKUL = $servisyetkili->netsiskullanici;
                $stokurs->DUZELTMETARIHI = date('Y-m-d H:i:s');
                $stokurs->KAYIT_EDILSIN = 1;
                $stokurs->BELGE_TIPI = 'B';
                $stokurs->save();
            } catch (Exception $e) {
                Log::error($e);
                return array('durum' => '0', 'text' => 'Üretim Sonu Kayıdı Netsis Tarafında Güncellenemedi');
            }

            $urunler = UretimSonuUrun::where('uretimsonu_id', $uretimsonukayitid)->get();
            $eklenenler = array();
            foreach ($urunler as $urun) {
                try {
                    $urunstokkod = NetsisStokKod::find($urun->netsisstokkod_id);
                    $seritra = Seritra::where('KAYIT_TIPI','A')->where('STRA_INC',$stokurs->INCKEYNO)
                        ->where('SUBE_KODU',$stokurs->SUBE_KODU)->where('BELGETIP','C')->where('SERI_NO',$urun->serino)->first();
                    if(!$seritra){
                        if(Seritra::where('KAYIT_TIPI','A')->where('STOK_KODU',BackendController::ReverseTrk($urunstokkod->kodu))->where('SERI_NO',$urun->serino)
                            ->where('SUBE_KODU',$stokurs->SUBE_KODU)->where('BELGETIP','C')->first()){
                            return array('durum' => '0', 'text' => $urun->serino.' Seri Numarasına Ait Bu Stok Kodunda Ürün Mevcut!');
                        }
                        $seritra = new Seritra;
                        $seritra->KAYIT_TIPI = 'A';
                        $seritra->SERI_NO = $urun->serino;
                        $seritra->STOK_KODU = BackendController::ReverseTrk($urunstokkod->kodu);
                        $seritra->STRA_INC = $stokurs->INCKEYNO;
                        $seritra->TARIH = $stokurs->URETSON_TARIH;
                        $seritra->ACIK1 = '';
                        $seritra->ACIK2 = '';
                        $seritra->GCKOD = 'G';
                        $seritra->MIKTAR = 1;
                        $seritra->MIKTAR2 = 0;
                        $seritra->BELGENO = $stokurs->URETSON_FISNO;
                        $seritra->BELGETIP = 'C';
                        $seritra->HARACIK = 'Uretim';
                        $seritra->SUBE_KODU = $stokurs->SUBE_KODU;
                        $seritra->DEPOKOD = $stokurs->URETSON_DEPO;
                        $seritra->SIPNO = $stokurs->URETSON_SIPNO;
                        $seritra->KARSISERI = '';
                        $seritra->YEDEK1 = '';
                        $seritra->YEDEK4 = 0;
                        $seritra->ONAYTIPI = 'A';
                        $seritra->ONAYNUM = 0;
                        $seritra->ACIK3 = '';
                        $seritra->save();
                        $sira = DB::connection('sqlsrv2')->select("select top(1)SIRA_NO from tblseritra where SUBE_KODU=" . $stokurs->SUBE_KODU . " and STRA_INC=" . $stokurs->INCKEYNO . " order by SIRA_NO desc");
                        $sirano = ($sira[0]->SIRA_NO);
                        if (in_array($sirano, $eklenenler)) { //gelen sırano zaten ekliyse kaydedememiş demektir
                            return array('durum' => '0', 'text' => 'Üretilen Ürünün Seri Numarası Netsis Tarafına Kaydedilemedi');
                        } else { //kaydetme başarılı
                            array_push($eklenenler, $sirano);
                            $urun->sirano = $sirano;
                            $urun->save();
                        }
                    }else{
                        $seritra->TARIH = $stokurs->URETSON_TARIH;
                        $seritra->DEPOKOD = $stokurs->URETSON_DEPO;
                        $seritra->save();
                    }
                } catch (Exception $e) {
                    Log::error($e);
                    return array('durum' => '0', 'text' => 'Üretilen Ürünlerin Seri Numarası Netsis Tarafına Kaydedilirken Hata Oluştu');
                }
            }

            $kullanilanlist = UretimSonuKullanilan::where('uretimsonu_id',$uretimsonukayitid)->get()->toArray();
            $uretimsonukullanilanlar = BackendController::UretimSonuKullanilanGrupla($kullanilanlist);
            $kullanilanlar = array();
            $toplamharcama = 0;
            try {
                foreach ($uretimsonukullanilanlar as $urun) {
                    $urunstokkod = NetsisStokKod::find($urun['kod']);
                    $sthar = Sthar::find($urun['urun'][0]['inckeyno']);
                    if(!$sthar){
                        $sthar = new Sthar;
                        $sthar->STOK_KODU = BackendController::ReverseTrk($urunstokkod->kodu);
                        $sthar->FISNO = $stokurs->URETSON_FISNO;
                        $sthar->STHAR_GCMIK = $urun['adet'];
                        $sthar->STHAR_GCKOD = 'C';
                        $sthar->STHAR_TARIH = $stokurs->URETSON_TARIH;
                        $sthar->STHAR_DOVTIP = 0;
                        $sthar->STHAR_DOVFIAT = 0;
                        if(count($urun['urun'])>1){
                            $urunharcama = 0;
                            foreach ($urun['urun'] as $kullanilan){
                                if(is_null($kullanilan['uretimurun_id'])){ //kullanılan alt ürün ise
                                    $kullanilan['uretimsonuurun'] = UretimSonuUrun::where('uretimsonu_id',$kullanilan['uretimsonu_id'])->first();
                                    $kullanilan['uretimsonualturun'] = UretimSonuUrun::where('uretimsonuurun_id',$kullanilan['uretimsonuurun']->id)->first();
                                    $urunharcama += ($kullanilan['urunadet']*$kullanilan['uretimsonualturun']->birimfiyat);
                                }else{
                                    $kullanilan['uretimurun'] = UretimUrun::find($kullanilan['uretimurun_id']);
                                    $urunharcama += ($kullanilan['urunadet']*$kullanilan['uretimurun']->birimfiyat);
                                }
                            }
                            $birimfiyat = $urunharcama/$urun['adet'];
                        }else{
                            if(is_null($urun['urun'][0]['uretimurun_id'])){ //kullanılan alt ürün ise
                                $urun['urun'][0]['uretimsonuurun'] = UretimSonuUrun::where('uretimsonu_id',$urun['urun'][0]['uretimsonu_id'])->first();
                                $urun['urun'][0]['uretimsonualturun'] = UretimSonuUrun::where('uretimsonuurun_id',$urun['urun'][0]['uretimsonuurun']->id)->first();
                                $birimfiyat = $urun['urun'][0]['uretimsonualturun']->birimfiyat;
                            }else {
                                $uretimurun = UretimUrun::find($urun['urun'][0]['uretimurun_id']);
                                $birimfiyat = $uretimurun->birimfiyat;
                            }
                        }
                        $sthar->STHAR_NF = $birimfiyat;
                        $sthar->STHAR_BF = $birimfiyat;
                        $toplamharcama += ($sthar->STHAR_GCMIK*$sthar->STHAR_NF);

                        $sthar->STHAR_KDV = 0;
                        $sthar->DEPO_KODU = $urun['depokodu'];
                        $sthar->STHAR_ACIKLAMA = BackendController::ReverseTrk($stokurs->URETSON_MAMUL);
                        $sthar->STHAR_FTIRSIP = null;
                        $sthar->LISTE_FIAT = 0;
                        $sthar->STHAR_HTUR = 'C';
                        $sthar->STHAR_ODEGUN = null;
                        $sthar->STHAR_BGTIP = 'V';
                        $sthar->STHAR_KOD1 = null;
                        $sthar->STHAR_SIPNUM = $stokurs->URETSON_SIPNO;
                        $sthar->STHAR_CARIKOD = null;
                        $sthar->PLASIYER_KODU = null;
                        $sthar->SIRA = 0;
                        $sthar->STRA_SIPKONT = 0;
                        $sthar->OLCUBR = 0;
                        $sthar->VADE_TARIHI = null;
                        $sthar->SUBE_KODU = $stokurs->SUBE_KODU;
                        $sthar->D_YEDEK10 = null;
                        $sthar->PROJE_KODU = 1;
                        $sthar->save();
                        $id = DB::connection('sqlsrv2')->select("select top(1)INCKEYNO from tblsthar where SUBE_KODU=" . $stokurs->SUBE_KODU . " and ".($stokurs->URETSON_FISNO==null ? "FISNO is null" : "FISNO='" . $stokurs->URETSON_FISNO."'")." and STHAR_FTIRSIP is null order by INCKEYNO desc");
                        $inckeyno = ($id[0]->INCKEYNO);
                        if (in_array($inckeyno, $kullanilanlar)) { //girilen inckey zaten ekliyse kaydedememiş demektir
                            return array('durum' => '0', 'text' => 'Kullanılan Malzemenin Stok Hareketi Netsis Tarafına Kaydedilemedi');
                        } else { //kaydetme başarılı
                            array_push($kullanilanlar, $inckeyno);
                            if(count($urun['urun'])>1){
                                foreach ($urun['urun'] as $kullanilan){
                                    UretimSonuKullanilan::find($kullanilan['id'])->update(['inckeyno'=>$inckeyno]);
                                }
                            }else{
                                UretimSonuKullanilan::find($urun['urun'][0]['id'])->update(['inckeyno'=>$inckeyno]);
                            }
                        }
                    }else{
                        $sthar->STHAR_GCMIK = $urun['adet'];
                        $sthar->STHAR_TARIH = $stokurs->URETSON_TARIH;
                        if(count($urun['urun'])>1){
                            $urunharcama = 0;
                            foreach ($urun['urun'] as $kullanilan){
                                if(is_null($kullanilan['uretimurun_id'])){ //kullanılan alt ürün ise
                                    $kullanilan['uretimsonuurun'] = UretimSonuUrun::where('uretimsonu_id',$kullanilan['uretimsonu_id'])->first();
                                    $kullanilan['uretimsonualturun'] = UretimSonuUrun::where('uretimsonuurun_id',$kullanilan['uretimsonuurun']->id)->first();
                                    $urunharcama += ($kullanilan['urunadet']*$kullanilan['uretimsonualturun']->birimfiyat);
                                }else{
                                    $kullanilan['uretimurun'] = UretimUrun::find($kullanilan['uretimurun_id']);
                                    $urunharcama += ($kullanilan['urunadet']*$kullanilan['uretimurun']->birimfiyat);
                                }
                            }
                            $birimfiyat = $urunharcama/$urun['adet'];
                        }else{
                            if(is_null($urun['urun'][0]['uretimurun_id'])){ //kullanılan alt ürün ise
                                $urun['urun'][0]['uretimsonuurun'] = UretimSonuUrun::where('uretimsonu_id',$urun['urun'][0]['uretimsonu_id'])->first();
                                $urun['urun'][0]['uretimsonualturun'] = UretimSonuUrun::where('uretimsonuurun_id',$urun['urun'][0]['uretimsonuurun']->id)->first();
                                $birimfiyat = $urun['urun'][0]['uretimsonualturun']->birimfiyat;
                            }else {
                                $uretimurun = UretimUrun::find($urun['urun'][0]['uretimurun_id']);
                                $birimfiyat = $uretimurun->birimfiyat;
                            }
                        }
                        $sthar->STHAR_NF = $birimfiyat;
                        $sthar->STHAR_BF = $birimfiyat;
                        $toplamharcama += ($sthar->STHAR_GCMIK*$sthar->STHAR_NF);

                        $sthar->DEPO_KODU = $urun['depokodu'];
                        $sthar->save();
                    }
                }
            } catch (Exception $e) {
                Log::error($e);
                return array('durum' => '0', 'text' => 'Kullanılan Ürünlerin Stok Hareketi Netsis Tarafına Kaydedilirken Hata Oluştu');
            }

            try {
                $uretimsonuurun = $urunler->first();
                $sthar = Sthar::find($uretimsonuurun->inckeyno);
                if(!$sthar)
                    return array('durum' => '0', 'text' => 'Üretilen Ürünün Üretim Sonu Kayıdı Stok Hareketi Bulunamadı');
                $sthar->STHAR_GCMIK = $stokurs->URETSON_MIKTAR;
                $sthar->STHAR_TARIH = $stokurs->URETSON_TARIH;
                $birimfiyat = $toplamharcama/$stokurs->URETSON_MIKTAR;
                $sthar->STHAR_NF = $birimfiyat;
                $sthar->STHAR_BF = $birimfiyat;
                $sthar->DEPO_KODU = $uretimsonukayit->girisdepo;
                $sthar->save();
                foreach ($urunler as $urun) {
                    $urun->inckeyno = $sthar->INCKEYNO;
                    $urun->birimfiyat = $birimfiyat;
                    $urun->save();
                }
            } catch (Exception $e) {
                Log::error($e);
                return array('durum' => '0', 'text' => 'Kullanılan Ürünlerin Stok Hareketi Netsis Tarafına Kaydedilirken Hata Oluştu');
            }

            if(!$uretimsonukayit->adet>=$isemri->MIKTAR){
                $isemri->KAPALI='E';
                $isemri->save();
            }
            return array('durum' => '1', 'id' => $stokurs->INCKEYNO);

        } catch (Exception $e) {
            Log::error($e);
            return array('durum'=>'0','text'=>'Üretim Sonu Kayıdı Tamamlanamadı');
        }
    }

    public static function UretimSonuKayitTemizle($uretimsonukayitid,$durum=false){
        $uretimsonukayit=UretimSonu::find($uretimsonukayitid);
        $isemri = IsEmri::find(BackendController::ReverseTrk($uretimsonukayit->isemrino));
        $servisyetkili = ServisYetkili::where('kullanici_id', Auth::user()->id)->first();
        if (!$servisyetkili) {
            return array('durum' => '0', 'text' => 'Üretim Sonu Kayıdı Silme Yetkiniz Yok');
        }
	    if($uretimsonukayit){
	        if($durum){ //tümü temizlenecek
                try {
                    $stokurs = StokUrs::find($uretimsonukayit->inckeyno);
                    if($stokurs){
                        $stokursek = StokUrsEk::where('INCKEYNOEK',$uretimsonukayit->inckeyno)->first();
                        if($stokursek)
                            $stokursek->delete();
                    }
                    try {
                        Seritra::where('STRA_INC',$uretimsonukayit->inckeyno)->delete();
                    } catch (Exception $e) {
                        Log::error($e);
                        return array('durum' => '0', 'text' => 'Üretilen Ürünlerin Seri Numarası Netsis Tarafından Silinirken Hata Oluştu');
                    }
                    try {
                        if($stokurs)
                            Sthar::where('STHAR_HTUR','C')->where('FISNO',$stokurs->URETSON_FISNO)->delete();
                    } catch (Exception $e) {
                        Log::error($e);
                        return array('durum' => '0', 'text' => 'Üretilen Ürünlerin Seri Numarası Netsis Tarafından Silinirken Hata Oluştu');
                    }
                    try {
                        if($stokurs)
                            $stokurs->delete();
                    } catch (Exception $e) {
                        Log::error($e);
                        return array('durum' => '0', 'text' => 'Üretim Sonu Kayıdı Netsis Tarafından Silinemedi');
                    }
                    $isemri->KAPALI='H';
                    $isemri->save();
                } catch (Exception $e) {
                    Log::error($e);
                    return array('durum' => '0', 'text' => 'Üretim Sonu Kayıdı Netsis Tarafından Silinemedi');
                }
            }else{
                $stokurs = StokUrs::find($uretimsonukayit->inckeyno);
                if(!$stokurs){
                    return array('durum' => '0', 'text' => 'Silinecek Üretim Sonu Kayıdı Bulunamadı');
                }
                try {
                    $stokurs->URETSON_DEPO = $uretimsonukayit->girisdepo;
                    $stokurs->URETSON_TARIH = $uretimsonukayit->guncellenmetarihi;
                    $stokurs->URETSON_MIKTAR = $uretimsonukayit->adet;
                    $stokurs->URETSON_MALY1 = 0.00;
                    $stokurs->URETSON_MALY2 = 0.00;
                    $stokurs->SUBE_KODU = 1;
                    $stokurs->I_YEDEK1 = $uretimsonukayit->cikisdepo;
                    $stokurs->I_YEDEK2 = 0;
                    $stokurs->PROJE_KODU = 1;
                    $stokurs->ACIKLAMA = $isemri->ACIKLAMA;
                    $stokurs->ONCELIK = 0;
                    $stokurs->save();
                } catch (Exception $e) {
                    Log::error($e);
                    return array('durum' => '0', 'text' => 'Üretim Sonu Kayıdı için Netsis Tarafında Geri Alma İşlemi Yapılamadı');
                }
                $urunler = UretimSonuUrun::where('uretimsonu_id', $uretimsonukayitid)->get();
                $seritralar = Seritra::where('KAYIT_TIPI','A')->where('STRA_INC',$stokurs->INCKEYNO)
                    ->where('SUBE_KODU',$stokurs->SUBE_KODU)->where('BELGETIP','C')->get();
                try {
                    foreach ($seritralar as $seritra) {
                        $urun = UretimSonuUrun::where('uretimsonu_id', $uretimsonukayitid)->where('serino',$seritra->SERI_NO)->first();
                        if($urun){
                            $urunstokkod = NetsisStokKod::find($urun->netsisstokkod_id);
                            $seritra->STOK_KODU = BackendController::ReverseTrk($urunstokkod->kodu);
                            $seritra->TARIH = $stokurs->URETSON_TARIH;
                            $seritra->DEPOKOD = $stokurs->URETSON_DEPO;
                            $seritra->save();
                        }else{
                            $seritra->delete();
                        }
                    }
                } catch (Exception $e) {
                    Log::error($e);
                    return array('durum' => '0', 'text' => 'Üretilen Ürünlerin Seri Numarası için Netsis Tarafında Geri Alma İşlemi Yapılamadı');
                }

                $kullanilanlist = UretimSonuKullanilan::where('uretimsonu_id',$uretimsonukayitid)->get()->toArray();
                $uretimsonukullanilanlar = BackendController::UretimSonuKullanilanGrupla($kullanilanlist);
                $toplamharcama = 0;
                $stharlar = Sthar::where('FISNO',$stokurs->URETSON_FISNO)->where('STHAR_GCKOD','C')->get(array('INCKEYNO'));
                $stharhepsi = array();
                foreach($stharlar as $sthar){
                    array_push($stharhepsi,$sthar->INCKEYNO);
                }
                try {
                    foreach ($uretimsonukullanilanlar as $urun) {
                        $urunstokkod = NetsisStokKod::find($urun['kod']);
                        $sthar = Sthar::find($urun['urun'][0]['inckeyno']);
                        if(!$sthar){
                            return array('durum' => '0', 'text' => 'Silinecek Üretim Sonu Kayıdı Stok Hareketi Bulunamadı');
                        }
                        $sthar->STOK_KODU = BackendController::ReverseTrk($urunstokkod->kodu);
                        $sthar->STHAR_GCMIK = $urun['adet'];
                        $sthar->STHAR_TARIH = $stokurs->URETSON_TARIH;
                        if(count($urun['urun'])>1){
                            $urunharcama = 0;
                            foreach ($urun['urun'] as $kullanilan){
                                $kullanilan['uretimurun'] = UretimUrun::find($kullanilan['uretimurun_id']);
                                $urunharcama += ($kullanilan['urunadet']*$kullanilan['uretimurun']->birimfiyat);
                            }
                            $birimfiyat = $urunharcama/$urun['adet'];
                        }else{
                            $uretimurun = UretimUrun::find($urun['urun'][0]['uretimurun_id']);
                            $birimfiyat = $uretimurun->birimfiyat;
                        }
                        $sthar->STHAR_NF = $birimfiyat;
                        $sthar->STHAR_BF = $birimfiyat;
                        $toplamharcama += ($sthar->STHAR_GCMIK*$sthar->STHAR_NF);
                        $sthar->DEPO_KODU = $urun['depokodu'];
                        $sthar->save();
                        $stharhepsi=array_diff($stharhepsi, array($sthar->INCKEYNO));
                    }
                    foreach ($stharhepsi as $stharinckey){
                        Sthar::find($stharinckey)->delete();
                    }
                } catch (Exception $e) {
                    Log::error($e);
                    return array('durum' => '0', 'text' => 'Kullanılan Ürünlerin Stok Hareketi için Netsis Tarafında Geri Alma İşlemi Yapılamadı');
                }

                try {
                    $uretimsonuurun = $urunler->first();
                    $sthar = Sthar::find($uretimsonuurun->inckeyno);
                    if(!$sthar)
                        return array('durum' => '0', 'text' => 'Silinecek Üretim Sonu Kayıdı Stok Hareketi Bulunamadı');
                    $sthar->STHAR_GCMIK = $stokurs->URETSON_MIKTAR;
                    $sthar->STHAR_TARIH = $stokurs->URETSON_TARIH;
                    $birimfiyat = $toplamharcama/$stokurs->URETSON_MIKTAR;
                    $sthar->STHAR_NF = $birimfiyat;
                    $sthar->STHAR_BF = $birimfiyat;
                    $sthar->DEPO_KODU = $uretimsonukayit->girisdepo;
                    $sthar->save();
                } catch (Exception $e) {
                    Log::error($e);
                    return array('durum' => '0', 'text' => 'Üretilen Ürünlerin Stok Hareketi için Netsis Tarafında Geri Alma İşlemi Yapılamadı');
                }
                if(!$uretimsonukayit->adet>=$isemri->MIKTAR){
                    $isemri->KAPALI='E';
                }else{
                    $isemri->KAPALI='H';
                }
                $isemri->save();
            }
        }else{
            return array('durum' => '0', 'text' => 'Üretim Sonu Kayıdı Bulunamadı.');
        }
        return array('durum' => '1');
    }

    public static function TakipNo($depogelenid,$inc){
        if ($depogelenid == "") {
            return "";
        }
        $digits = str_split($depogelenid);
        $index = -1;
        for ($i = 0; $i < count($digits); $i++) {
            if (!is_numeric($digits[$i])) {
                $index = $i;
            }
        }
        $digitlength = 8 - ($index+1);
        $pattern = "%0" . $digitlength . "d";
        return 'M'.preg_replace_callback('/(\\d+)$/', function ($match) use ($pattern, $inc) { return (sprintf($pattern, ($match[0] + $inc)));}, $depogelenid);
    }

    public static function SatisFaturasiTemizle($id){
        $sayacsatis=SubeSayacSatis::find($id);
        if($sayacsatis){
            $fatirsno = $sayacsatis->faturano;
            $secilenler = explode(',', $sayacsatis->secilenler);
            $sayaclar = explode(';', $sayacsatis->sayaclar);
            $adetler = explode(',', $sayacsatis->adet);
            $birimfiyatlar = explode(',', $sayacsatis->birimfiyat);
            $ucretsizler = explode(',', $sayacsatis->ucretsiz);
            $urunler = array();
            $status = 0;
            if ($sayacsatis->odemetipi == 4 || $sayacsatis->odemetipi == 5 || $sayacsatis->odemetipi == 6) {
                $status = 1;
            }
            for ($i = 0; $i < count($secilenler); $i++) {
                $urun = SubeUrun::find($secilenler[$i]);
                $urun->adet = $adetler[$i];
                $urun->fiyat = $birimfiyatlar[$i];
                $urun->ucretsiz = $ucretsizler[$i];
                $urun->stokkodu = NetsisStokKod::find($urun->netsisstokkod_id);
                if ($urun->baglanti) {
                    $sayaclist = explode(',', $sayaclar[$i]);
                    $urun->sayaclar = AboneSayac::where('sayacadi_id', $urun->sayacadi_id)->where('sayaccap_id', $urun->sayaccap_id)
                        ->where('uretimyer_id', $sayacsatis->uretimyer_id)->whereIn('id', $sayaclist)->get();
                } else {
                    $urun->sayaclar = array();
                }
                array_push($urunler, $urun);
            }
            try {
                $fatura = Fatuirs::where('SUBE_KODU', $sayacsatis->subekodu)->where('FTIRSIP', 1)->where('FATIRS_NO', $fatirsno)->first();
                if ($fatura) {
                    $fatura->delete();
                }
                $faturaek = Fatuek::where('SUBE_KODU', $sayacsatis->subekodu)->where('FKOD', 1)->where('FATIRSNO', $fatirsno)->first();
                if ($faturaek) {
                    $faturaek->delete();
                }

                $inckey = array();
                for ($i = 0; $i < count($urunler); $i++) {
                    $faturakalem = Sthar::where('SUBE_KODU', $sayacsatis->subekodu)->where('STHAR_FTIRSIP', 1)->where('FISNO', $fatirsno)
                        ->where('STOK_KODU', $urunler[$i]->stokkodu->kodu)->first();
                    if ($faturakalem) {
                        array_push($inckey, $faturakalem->INCKEYNO);
                        $faturakalem->delete();
                    }
                }
                BackendController::SeriSil($sayacsatis, $inckey);
                if (!$status) { //AÇIK FATURA KASA KAYIDI YAPILMIYOR
                    BackendController::KasaSil($sayacsatis, 1);
                }
                BackendController::CariHareketSil($sayacsatis);
                BackendController::MuhasebeFisSil($sayacsatis);

                BackendController::ServisBilgisiSil($sayacsatis, 0); // 0 Satış 1 Tamir sonrası servis 2 arıza kontrolü
                return array('durum' => '1');
            } catch (Exception $e) {
                Log::error($e);
                return array('durum' => '0', 'text' => 'Satış Faturası Netsis Tarafından Silinemedi');
            }
        }else{
            return array('durum' => '0', 'text' => 'Satış Kayıdı Bulunamadı.');
        }
    }

    public static function SayacBorcuHesapla($sayacgelen){
	    $arizakayit = ArizaKayit::where('sayacgelen_id',$sayacgelen->id)->first();
	    if($arizakayit){
	        $mekanikfark = $arizakayit->mekanik-$arizakayit->ilkmekanik;
	        $harcananfark = $arizakayit->harcanankredi-$arizakayit->ilkharcanan;
	        $kredifark = $arizakayit->kalankredi-$arizakayit->ilkkredi;
	        if($harcananfark>$mekanikfark){
	            $fark = $harcananfark-$kredifark;
	            if($fark>0)
	                return $fark;
            }
        }
	    return 0;
    }

    public static function SmsGonder($serviskayit){
        try {
            $username = '5336024556';
            $password = '6637omer';
            $orgin_name = 'MANASENERJI';
            $abonetahsis = AboneTahsis::find($serviskayit->abonetahsis_id);
            $abonesayac = AboneSayac::find($abonetahsis->abonesayac_id);
            $fark = $serviskayit->sonendeks - $serviskayit->ilkendeks + $serviskayit->sayacborcu;
            $birim = $abonesayac->sayactur_id == 2 ? 'kWh' : 'm3';
            if ($serviskayit->tipi == 2)
                if ($serviskayit->sokulmedurumu)
                    if(!$serviskayit->servissayaci)
                        $mesaj = $abonesayac->serino . ' nolu sayacınız arıza nedeniyle sökülmüş. Tamir işlemi sonrasında tekrar bilgi verilecektir'; //90 en az
                    else
                        $mesaj = $abonesayac->serino . ' nolu sayacınız arıza nedeniyle sökülmüş yerine ' . $serviskayit->ilkendeks . ' ' . $birim . ' endeksli' .
                            ' geçici sayaç takılmıştır. Tamir işlemi sonrasında tekrar bilgi verilecektir'; //148 en az
                else
                    $mesaj = $abonesayac->serino . ' nolu sayacınızın arızası ' . date("d-m-Y", strtotime($serviskayit->kapanmatarihi)) . ' tarihinde ' . date('H:i') . ' saatinde giderilmiştir.' .
                        ' Bilginize'; //92 en az
            else if ($serviskayit->tipi == 0)
                $mesaj = $abonesayac->serino . ' nolu sayacınızın montajı ' . date("d-m-Y", strtotime($serviskayit->kapanmatarihi)) . ' tarihinde yapılmıştır.' .
                    ' Kredi Yüklemesi ve vana kontrollerinizi yaptıktan sonra kullanıma başlayabilirsiniz'; //149 en az
            else if ($serviskayit->tipi == 1)
                $mesaj = $abonesayac->serino . ' nolu sayacınızın tamir işlemi gerçekleştirilmiştir. Geçici sayaçtan yaptığınız ' .
                    $fark . ' ' . $birim . ($birim == "m3" ? " lük" : " lık") . ' kullanımı kredi yükleme sırasında tahsil edilecektir'; //148 en az
            else
                $mesaj = "";
            $mesajup = self::StrtoUpper($mesaj);
            $search = array("ç", "ı", "ğ", "ö", "ş", "ü", "Ç", "İ", "Ğ", "Ö", "Ş", "Ü");
            $replace = array("c", "i", "g", "o", "s", "u", "C", "I", "G", "O", "S", "U");
            $mesajnor = str_replace($search, $replace, $mesajup);
            $xml = <<<EOS
   		 <request>
   			 <authentication>
   				 <username>{$username}</username>
   				 <password>{$password}</password>
   			 </authentication>

   			 <order>
   	    		 <sender>{$orgin_name}</sender>
   	    		 <sendDateTime></sendDateTime>
   	    		 <message>
   	        		 <text>{$mesajnor}</text>
   	        		 <receipents>
   	            		 <number>{$serviskayit->ilgilitel}</number>
   	        		 </receipents>
   	    		 </message>
   			 </order>
   		 </request>
EOS;
            $result = BackendController::sendRequest('http://api.iletimerkezi.com/v1/send-sms', $xml, array('Content-Type: text/xml'));
            $smsresult = $result;
            try{
                $resultarray = XmlToArray::convert($result);
                $smslog = new SmsLog;
                $smslog->islem_id = $serviskayit->id;
                $smslog->islemtipi = 1;
                $smslog->mesaj = $mesajnor;
                $smslog->ilgilitel = $serviskayit->ilgilitel;
                $smslog->durum = $resultarray['status']['code'];
                $smslog->response = $smsresult;
                $smslog->tarih = date("Y-m-d H:i");
                $smslog->save();
                if ($smslog->durum == 200) //düzenlenecek resultta göre
                    return 1;
                else
                    return 0;
            } catch (Exception $e) {
                Log::error($e);
                $smslog = new SmsLog;
                $smslog->islem_id = $serviskayit->id;
                $smslog->islemtipi = 1;
                $smslog->mesaj = $mesajnor;
                $smslog->ilgilitel = $serviskayit->ilgilitel;
                $smslog->durum = 999;
                $smslog->response = $smsresult;
                $smslog->tarih = date("Y-m-d H:i");
                $smslog->save();
                if ($smslog->durum == 200) //düzenlenecek resultta göre
                    return 1;
                else
                    return 0;
            }
        } catch (Exception $e) {
            Log::error($e);
        }
    }

    public static function sendRequest($site_name, $send_xml, $header_type)
    {
        try {
            //die('SITENAME:'.$site_name.'SEND XML:'.$send_xml.'HEADER TYPE '.var_export($header_type,true));
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $site_name);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $send_xml);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header_type);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 120);
            $result = curl_exec($ch);
            return $result;
        } catch (Exception $e) {
            Log::error($e);
            return 'Api kullanımı başarısız.';
        }
    }

    public static function MuadilKontrol($uretimurun){
	    // muadil kullanım durumu kontrol edilecek uretim sonu kayıt
        $uretimsonukullanilan = UretimSonuKullanilan::where('uretimurun_id',$uretimurun->id)->first();
        if($uretimsonukullanilan)
            return false;
	    return true;
    }

    public static function UcretlendirilenDepoTeslimDurum($ucretlendirilen_id){
	    $ucretlendirilen = Ucretlendirilen::find($ucretlendirilen_id);
	    $onaylanan = Onaylanan::where('ucretlendirilen_id',$ucretlendirilen_id)->first();
	    if(!$onaylanan)
	        return true;
	    $servistakipler = ServisTakip::where('ucretlendirilen_id',$ucretlendirilen_id)->get();
	    $secilenler = array();
	    $flag=0;
	    foreach ($servistakipler as $servistakip){
	        array_push($secilenler,$servistakip->sayacgelen_id);
        }
	    $depoteslimler = DepoTeslim::where('netsiscari_id',$ucretlendirilen->netsiscari_id)->where('depodurum',0)->get();
	    foreach ($depoteslimler as $depoteslim){
	        $depoteslimlist = explode(',',$depoteslim->secilenler);
	        foreach ($secilenler as $secilen){
                if(BackendController::Listedemi($secilen,$depoteslimlist)){
                    $flag++;
                }
            }
        }
	    if($flag>=count($secilenler))
	        return true;
	    return false;
    }

    public static function ArizaFiyatDepoTeslimDurum($arizafiyat_id){
	    $servistakip = ServisTakip::where('arizafiyat_id',$arizafiyat_id)->first();
	    if(!$servistakip->onaylanan_id)
	        return true;
        $netsiscari_id = $servistakip->netsiscari_id;
	    $secilen=$servistakip->sayacgelen_id;
	    $depoteslimler = DepoTeslim::where('netsiscari_id',$netsiscari_id)->where('depodurum',0)->get();
	    foreach ($depoteslimler as $depoteslim){
	        $depoteslimlist = explode(',',$depoteslim->secilenler);
            if(BackendController::Listedemi($secilen,$depoteslimlist)){
                return true;
            }
        }
	    return false;
    }

    public static function AddNewConnection($new){
        $sqlsrv = array(  'driver'   => 'sqlsrv', 'host' => '192.168.100.12\NETSISDB', 'database' => $new, 'username' => 'SERVISTAKIP', 'password' => 'Servis..Takip!!', 'characterset' => 'utf8', 'prefix'   => '' );
        for($i=5;$i<10;$i++){
            if(!Config::offsetGet('database.connections.sqlsrv'.$i)){
                Config::offsetSet('database.connections.sqlsrv'.$i,$sqlsrv);
                return 'sqlsrv'.$i;
            }
        }
	}

    public static function DeleteConnection($name){
	    Config::offsetUnset('database.connections.'.$name);
	    return true;
	}
}
