<?php

/** @noinspection PhpUnusedLocalVariableInspection */

class DestekdatabaseController extends BackendController {

    /*public function getKategori() {
        return View::make('destekdatabase.kategori')->with(array('title'=>'Kategoriler'));
    }

    public function postKategorilist() {
        $query = EdKategori::select(array("edkategori.id","edkategori.kategori_adi","edkategori.ust_id"));
        return Datatables::of($query)
            ->addColumn('ust_kategori',function ($model) {
                if(!is_null($model->ust_id)){
                    $ust_kategori = EdKategori::find($model->ust_id);
                    return $ust_kategori->kategori_adi;
                }else{
                    return "";
                }
            })
            ->addColumn('islemler',function ($model) {
                $root = BackendController::getRootDizin();
                if(is_null($model->ust_id)){
                    return "<a class='btn btn-sm btn-warning' href='".$root."/destekdatabase/kategoriduzenle/".$model->id."'> Düzenle </a>";
                }else{
                    $kategoriurun=EdKategoriUrun::where('kategori_id',$model->id)->first();
                    $alt_kategori=EdKategori::where('ust_id',$model->id)->first();
                    if($kategoriurun || $alt_kategori){
                        return "<a class='btn btn-sm btn-warning' href='".$root."/destekdatabase/kategoriduzenle/".$model->id."'> Düzenle </a>";
                    }else{
                        return "<a class='btn btn-sm btn-warning' href='".$root."/destekdatabase/kategoriduzenle/".$model->id."'> Düzenle </a>".
                            "<a href='#portlet-delete' data-toggle='modal' data-id='".$model->id."' class='btn btn-sm btn-danger delete' data-original-title='' title=''>Sil</a>";
                    }
                }
            })
            ->make(true);
    }

    public function getKategoriekle() {
        $kategoriler = EdKategori::all();
        return View::make('destekdatabase.kategoriekle',compact('kategoriler'))->with(array('title'=>'Kategori Ekle'));
    }

    public function postKategoriekle() {
        try {
            $rules = ['kategori_adi' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $kategori_adi = Input::get('kategori_adi');
            $ust_kategori = Input::get('ust_kategori')=="" ? null : Input::get('ust_kategori');
            if(EdKategori::where('kategori_adi',$kategori_adi)->first()){
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Bu Kategori Zaten Tanımlı!', 'type' => 'warning'));
            }
            $kategori = EdKategori::create(array(
                'kategori_adi'=>$kategori_adi,
                'ust_id'=>$ust_kategori,
                'slug'=>Str::slug($kategori_adi)
            ));
            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-plus-circle', $kategori_adi . ' İsimli Kategori Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Kategori Numarası:' . $kategori->id);
            DB::commit();
            return Redirect::to('destekdatabase/kategori')->with(array('mesaj' => 'true', 'title' => 'Kategori Eklendi', 'text' => 'Kategori Başarıyla Eklendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kategori Eklenemedi', 'text' => 'Kategori Eklenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getKategoriduzenle($id) {
        $kategori = EdKategori::find($id);
        $kategoriler = EdKategori::all();
        return View::make('destekdatabase.kategoriduzenle',compact('kategori','kategoriler'))->with(array('title'=>'Kategori Bilgi Düzenleme Ekranı'));
    }

    public function postKategoriduzenle($id) {
        try {
            $rules = ['kategori_adi' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $kategori = EdKategori::find($id);
            $bilgi = $kategori;
            $kategori_adi = Input::get('kategori_adi');
            $ust_kategori = Input::get('ust_kategori')=="" ? null : Input::get('ust_kategori');
            if(EdKategori::where('kategori_adi',$kategori_adi)->where('id','<>',$id)->first()){
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Bu Kategori Zaten Tanımlı!', 'type' => 'warning'));
            }
            $kategori->kategori_adi = $kategori_adi;
            $kategori->ust_id = $ust_kategori;
            $kategori->slug = Str::slug($kategori_adi);
            $kategori->save();
            BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-edit', $bilgi->kategori_adi . ' İsimli Kategori Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Kategori Numarası:' . $bilgi->id);
            DB::commit();
            return Redirect::to('destekdatabase/kategori')->with(array('mesaj' => 'true', 'title' => 'Kategori Güncellendi', 'text' => 'Kategori Başarıyla Güncellendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kategori Güncellenemedi', 'text' => 'Kategori Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getUrun() {
        return View::make('destekdatabase.urun')->with(array('title'=>'Ürünler'));
    }

    public function postUrunlist() {
        $query = EdUrun::select(array("edurun.id","edurun.urun_adi"));
        return Datatables::of($query)
            ->addColumn('islemler',function ($model) {
                $root = BackendController::getRootDizin();
                $kategoriurun = EdKategoriUrun::where('urun_id',$model->id)->first();
                if($kategoriurun){
                    return "<a class='btn btn-sm btn-warning' href='".$root."/destekdatabase/urunduzenle/".$model->id."'> Düzenle </a>";
                }else{
                    return "<a class='btn btn-sm btn-warning' href='".$root."/destekdatabase/urunduzenle/".$model->id."'> Düzenle </a>".
                        "<a href='#portlet-delete' data-toggle='modal' data-id='".$model->id."' class='btn btn-sm btn-danger delete' data-original-title='' title=''>Sil</a>";
                }
            })
            ->make(true);
    }

    public function getUrunekle() {
        $urunler = EdUrun::all();
        return View::make('destekdatabase.urunekle',compact('urunler'))->with(array('title'=>'Ürün Ekle'));
    }

    public function postUrunekle() {
        try {
            $rules = ['urun_adi' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $urun_adi = Input::get('urun_adi');
            $isim = null;
            If (Input::hasFile('resim')) {
                $resim = Input::file('resim');
                $uzanti = $resim->getClientOriginalExtension();
                $isim = Str::slug($urun_adi) . Str::slug(str_random(5)) . '.' . $uzanti;
                $resim->move('assets/urun/', $isim);
            }
            $aciklama = Input::get('aciklama');
            if(EdUrun::where('urun_adi',$urun_adi)->first()){
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Bu Ürün Zaten Tanımlı!', 'type' => 'warning'));
            }
            $urun = EdUrun::create(array(
                'urun_adi'=>$urun_adi,
                'aciklama'=>$aciklama,
                'resim'=>$isim,
                'slug'=>Str::slug($urun_adi)
            ));
            BackendController::IslemEkle(1, Auth::user()->id, 'label-success', 'fa-plus-circle', $urun_adi . ' İsimli Ürün Eklendi.', 'Ekleyen:' . Auth::user()->adi_soyadi . ',Ürün Numarası:' . $urun->id);
            DB::commit();
            return Redirect::to('destekdatabase/urun')->with(array('mesaj' => 'true', 'title' => 'Ürün Eklendi', 'text' => 'Ürün Başarıyla Eklendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ürün Eklenemedi', 'text' => 'Ürün Eklenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getUrunduzenle($id) {
        $urun = EdUrun::find($id);
        return View::make('destekdatabase.urunduzenle',compact('urun'))->with(array('title'=>'Ürün Bilgi Düzenleme Ekranı'));
    }

    public function postUrunduzenle($id) {
        try {
            $rules = ['urun_adi' => 'required'];
            $validate = Validator::make(Input::all(), $rules);
            $messages = $validate->messages();
            if ($validate->fails()) {
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first() . '', 'type' => 'error'));
            }
            DB::beginTransaction();
            $urun = EdUrun::find($id);
            $bilgi = $urun;
            $eskiresim=$urun->resim;
            $ekliresim=Input::get('ekliresim');
            if($ekliresim!=$eskiresim)
                File::delete('assets/urun/' . $eskiresim . '');
            $urun_adi = Input::get('urun_adi');
            $isim = null;
            If (Input::hasFile('resim')) {
                $resim = Input::file('resim');
                $uzanti = $resim->getClientOriginalExtension();
                $isim = Str::slug($urun_adi) . Str::slug(str_random(5)) . '.' . $uzanti;
                $resim->move('assets/urun/', $isim);
            }
            $aciklama = Input::get('aciklama');
            if(EdUrun::where('urun_adi',$urun_adi)->where('id','<>',$id)->first()){
                Input::flash();
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Kaydetme Hatası', 'text' => 'Bu Ürün Zaten Tanımlı!', 'type' => 'warning'));
            }
            $urun->urun_adi = $urun_adi;
            $urun->aciklama = $aciklama;
            $urun->resim = $isim;
            $urun->slug = Str::slug($urun_adi);
            $urun->save();
            BackendController::IslemEkle(2, Auth::user()->id, 'label-warning', 'fa-edit', $bilgi->urun_adi . ' İsimli Ürün Güncellendi.', 'Güncelleyen:' . Auth::user()->adi_soyadi . ',Ürün Numarası:' . $bilgi->id);
            DB::commit();
            return Redirect::to('destekdatabase/urun')->with(array('mesaj' => 'true', 'title' => 'Ürün Güncellendi', 'text' => 'Ürün Başarıyla Güncellendi', 'type' => 'success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ürün Güncellenemedi', 'text' => 'Ürün Güncellenirken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getUrunsil($id){
        try {
            DB::beginTransaction();
            $urun = EdUrun::find($id);
            if($urun){
                $bilgi = $urun;
                $proje_urun = EdProjeUrun::where('urun_id', $id)->first();
                if ($proje_urun) {
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ürün Silinemez', 'text' => 'Ürün Bir Projede Mevcut!', 'type' => 'error'));
                }
                $urun->delete();
                BackendController::IslemEkle(3, Auth::user()->id, 'label-danger', 'fa-close', $bilgi->urun_adi . ' İsimli Ürün Silindi.', 'Silen:' . Auth::user()->adi_soyadi . ',Ürün Numarası:' . $bilgi->id);
                DB::commit();
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ürün Silindi', 'text' => 'Ürün Başarıyla Silindi.', 'type' => 'success'));
            }else{
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ürün Silinemedi', 'text' => 'Ürün Zaten Silinmiş.', 'type' => 'error'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Ürün Silinemedi', 'text' => 'Ürün Silinirken Sorun Oluştu.', 'type' => 'error'));
        }
    }*/

    public function getUrunler() {
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $kategoriurunler = EdKategoriUrun::groupBy(array('urun_id'))->get(array('urun_id'))->toArray();
        $urunler = EdUrun::whereNotIn('id',$kategoriurunler)->get();
        $tree = $this->getKategoritree();
        return View::make('destekdatabase.kategori_urun',compact('tree','urunler'))->with(array('title'=>'Kategoriler ve Ürünleri'));
    }

    public function getKategoritree(){
        $ust_kategoriler = EdKategori::whereNull('ust_id')->get();
        $tree ='';
        foreach ($ust_kategoriler as $kategori){
            if($kategori->alt_kategoriler()->count()>0){
                $tree .= '<li data-jstree=\'{ "type" : "kategori","opened" : true}\'><button style="margin-right: 5px" href="#portlet-ekle" data-toggle="modal" data-id="'.$kategori->id.'" data-adi="'.$kategori->kategori_adi.'" data-status="1" class="btn btn-sm btn-success ekle" ><i class="fa fa-plus-square"></i></button>
                <button style="margin-right: 5px" data-target="#kategoriguncelle" data-toggle="modal" data-id="'.$kategori->id.'" data-value="'.$kategori->kategori_adi.'" class="btn btn-sm btn-warning kategoriguncelle"><i class="fa fa-pencil-square"></i></button>'. $kategori->kategori_adi;
                $tree .= $this->altKategoriler($kategori);
            }elseif($kategori->urunler()->distinct()->count()>0){
                $tree .= '<li data-jstree=\'{ "type" : "kategori","opened" : true}\'><button style="margin-right: 5px" href="#portlet-ekle" data-toggle="modal" data-id="'.$kategori->id.'" data-adi="'.$kategori->kategori_adi.'" data-status="0" class="btn btn-sm btn-success ekle" ><i class="fa fa-plus-square"></i></button>
                <button style="margin-right: 5px" data-target="#kategoriguncelle" data-toggle="modal" data-id="'.$kategori->id.'" data-value="'.$kategori->kategori_adi.'" class="btn btn-sm btn-warning kategoriguncelle"><i class="fa fa-pencil-square"></i></button>'. $kategori->kategori_adi;
                $tree .= $this->urunler($kategori);
            }else{
                $tree .= '<li data-jstree=\'{ "type" : "kategori"}\'><button style="margin-right: 5px" href="#portlet-ekle" data-toggle="modal" data-id="'.$kategori->id.'" data-adi="'.$kategori->kategori_adi.'" data-status="2" class="btn btn-sm btn-success ekle" ><i class="fa fa-plus-square"></i></button>
                <button style="margin-right: 5px" data-target="#kategoriguncelle" data-toggle="modal" data-id="'.$kategori->id.'" data-value="'.$kategori->kategori_adi.'" class="btn btn-sm btn-warning kategoriguncelle"><i class="fa fa-pencil-square"></i></button>
                <button style="margin-right: 5px" href="#portlet-kategori-sil" data-toggle="modal" data-id="'.$kategori->id.'" class="btn btn-sm btn-danger kategori-sil" ><i class="fa fa-minus-square"></i></button>'. $kategori->kategori_adi;
            }
            $tree .='</li>';
        }
        return $tree;
    }

    public function altKategoriler($kategori){
        $html ='<ul>';
        foreach ($kategori->alt_kategoriler()->get() as $arr) {
            if($arr->alt_kategoriler()->count()>0){
                $html .='<li data-jstree=\'{ "type" : "kategori","opened" : true}\'><button style="margin-right: 5px" href="#portlet-ekle" data-toggle="modal" data-id="'.$arr->id.'" data-adi="'.$arr->kategori_adi.'" data-status="1" class="btn btn-sm btn-success ekle" ><i class="fa fa-plus-square"></i></button>
                <button style="margin-right: 5px" data-target="#kategoriguncelle" data-toggle="modal" data-id="'.$arr->id.'" data-value="'.$arr->kategori_adi.'" class="btn btn-sm btn-warning kategoriguncelle"><i class="fa fa-pencil-square"></i></button>'. $arr->kategori_adi;
                $html.= $this->altKategoriler($arr);
            }elseif($arr->urunler()->distinct()->count()>0){
                $html .='<li data-jstree=\'{ "type" : "kategori","opened" : true}\'><button style="margin-right: 5px" href="#portlet-ekle" data-toggle="modal" data-id="'.$arr->id.'" data-adi="'.$arr->kategori_adi.'" data-status="0" class="btn btn-sm btn-success ekle" ><i class="fa fa-plus-square"></i></button>
                <button style="margin-right: 5px" data-target="#kategoriguncelle" data-toggle="modal" data-id="'.$arr->id.'" data-value="'.$arr->kategori_adi.'" class="btn btn-sm btn-warning kategoriguncelle"><i class="fa fa-pencil-square"></i></button>'. $arr->kategori_adi;
                $html .= $this->urunler($arr);
            }else{
                $html .='<li data-jstree=\'{ "type" : "kategori"}\'><button style="margin-right: 5px" href="#portlet-ekle" data-toggle="modal" data-id="'.$arr->id.'" data-adi="'.$arr->kategori_adi.'" data-status="2" class="btn btn-sm btn-success ekle" ><i class="fa fa-plus-square"></i></button>
                <button style="margin-right: 5px" data-target="#kategoriguncelle" data-toggle="modal" data-id="'.$arr->id.'" data-value="'.$arr->kategori_adi.'" class="btn btn-sm btn-warning kategoriguncelle"><i class="fa fa-pencil-square"></i></button>
                <button style="margin-right: 5px" href="#portlet-kategori-sil" data-toggle="modal" data-id="'.$arr->id.'" class="btn btn-sm btn-danger kategori-sil" ><i class="fa fa-minus-square"></i></button>'. $arr->kategori_adi;
            }
            $html .='</li>';
        }
        $html .='</ul>';
        return $html;
    }

    public function urunler($kategori){
        $html ='<ul>';
        foreach ($kategori->urunler()->distinct()->get() as $urun) {
            if($urun->parcalar()->distinct()->count()>0){
                $html .='<li data-jstree=\'{ "type" : "urun","opened" : true }\'><button style="margin-right: 5px" href="#portlet-ekle" data-toggle="modal" data-id="'.$urun->id.'" data-adi="'.$urun->urun_adi.'" data-status="3" class="btn btn-sm btn-success ekle" ><i class="fa fa-plus-square"></i></button>
                <button style="margin-right: 5px" data-target="#urunguncelle" data-toggle="modal" data-id="'.$urun->id.'" data-value="'.$urun->urun_adi.'" class="btn btn-sm btn-warning urunguncelle"><i class="fa fa-pencil-square"></i></button>
                <button style="margin-right: 5px" href="#portlet-urun-sil" data-toggle="modal" data-id="'.$urun->id.'" class="btn btn-sm btn-danger urun-sil" ><i class="fa fa-minus-square"></i></button>'.$urun->urun_adi;
                $html .= $this->parcalar($urun);
            }else{
                $html .='<li data-jstree=\'{ "type" : "urun" }\'><button style="margin-right: 5px" href="#portlet-ekle" data-toggle="modal" data-id="'.$urun->id.'" data-adi="'.$urun->urun_adi.'" data-status="4" class="btn btn-sm btn-success ekle" ><i class="fa fa-plus-square"></i></button>
                <button style="margin-right: 5px" data-target="#urunguncelle" data-toggle="modal" data-id="'.$urun->id.'" data-value="'.$urun->urun_adi.'" class="btn btn-sm btn-warning urunguncelle"><i class="fa fa-pencil-square"></i></button>
                <button style="margin-right: 5px" href="#portlet-urun-sil" data-toggle="modal" data-id="'.$urun->id.'" class="btn btn-sm btn-danger urun-sil" ><i class="fa fa-minus-square"></i></button>'.$urun->urun_adi;
            }
            $html .='</li>';
        }
        $html .='</ul>';
        return $html;
    }

    public function parcalar($urun){
        $html ='<ul>';
        foreach ($urun->parcalar()->distinct()->get() as $parca) {
            if($parca->alt_parcalar()->count()>0){
                $html .='<li data-jstree=\'{ "type" : "parca","opened" : true }\'><button style="margin-right: 5px" href="#portlet-ekle" data-toggle="modal" data-id="'.$parca->id.'" data-adi="'.$parca->parca_adi.'" data-status="5" class="btn btn-sm btn-success ekle" ><i class="fa fa-plus-square"></i></button>
                <button style="margin-right: 5px" data-target="#parcaguncelle" data-toggle="modal" data-id="'.$parca->id.'" data-value="'.$parca->parca_adi.'" class="btn btn-sm btn-warning parcaguncelle"><i class="fa fa-pencil-square"></i></button>
                <button style="margin-right: 5px" href="#portlet-parca-sil" data-toggle="modal" data-id="'.$parca->id.'" class="btn btn-sm btn-danger parca-sil" ><i class="fa fa-minus-square"></i></button>'.$parca->parca_adi;
                $html.= $this->altParcalar($parca);
            }else{
                $html .='<li data-jstree=\'{ "type" : "parca" }\'><button style="margin-right: 5px" href="#portlet-ekle" data-toggle="modal" data-id="'.$parca->id.'" data-adi="'.$parca->parca_adi.'" data-status="6" class="btn btn-sm btn-success ekle" ><i class="fa fa-plus-square"></i></button>
                <button style="margin-right: 5px" data-target="#parcaguncelle" data-toggle="modal" data-id="'.$parca->id.'" data-value="'.$parca->parca_adi.'" class="btn btn-sm btn-warning parcaguncelle"><i class="fa fa-pencil-square"></i></button>
                <button style="margin-right: 5px" href="#portlet-parca-sil" data-toggle="modal" data-id="'.$parca->id.'" class="btn btn-sm btn-danger parca-sil" ><i class="fa fa-minus-square"></i></button>'.$parca->parca_adi;
            }
            $html .='</li>';
        }
        $html .='</ul>';
        return $html;
    }

    public function altParcalar($parca){
        $html ='<ul>';
        foreach ($parca->alt_parcalar()->get() as $arr) {
            if($arr->alt_parcalar()->count()>0){
                $html .='<li data-jstree=\'{ "type" : "parca","opened" : true }\'><button style="margin-right: 5px" href="#portlet-ekle" data-toggle="modal" data-id="'.$arr->id.'" data-adi="'.$arr->parca_adi.'" data-status="5" class="btn btn-sm btn-success ekle" ><i class="fa fa-plus-square"></i></button>
                <button style="margin-right: 5px" data-target="#parcaguncelle" data-toggle="modal" data-id="'.$arr->id.'" data-value="'.$arr->parca_adi.'" class="btn btn-sm btn-warning parcaguncelle"><i class="fa fa-pencil-square"></i></button>
                <button style="margin-right: 5px" href="#portlet-parca-sil" data-toggle="modal" data-id="'.$arr->id.'" class="btn btn-sm btn-danger parca-sil" ><i class="fa fa-minus-square"></i></button>'.$arr->parca_adi;
                $html.= $this->altParcalar($arr);
            }else{
                $html .='<li data-jstree=\'{ "type" : "parca" }\'><button style="margin-right: 5px" href="#portlet-ekle" data-toggle="modal" data-id="'.$arr->id.'" data-adi="'.$arr->parca_adi.'" data-status="6" class="btn btn-sm btn-success ekle" ><i class="fa fa-plus-square"></i></button>
                <button style="margin-right: 5px" data-target="#parcaguncelle" data-toggle="modal" data-id="'.$arr->id.'" data-value="'.$arr->parca_adi.'" class="btn btn-sm btn-warning parcaguncelle"><i class="fa fa-pencil-square"></i></button>
                <button style="margin-right: 5px" href="#portlet-parca-sil" data-toggle="modal" data-id="'.$arr->id.'" class="btn btn-sm btn-danger parca-sil" ><i class="fa fa-minus-square"></i></button>'.$arr->parca_adi;
            }
            $html .='</li>';
        }
        $html .='</ul>';
        return $html;
    }

    public function postKategoriurunekle(){
        try {
            $kategori_id = Input::get('kategori');
            $durum = Input::get('eklenecek');
            if($durum){
                $eklenecek = Input::get('altkategori');
            }else{
                $eklenecek = Input::get('urun');
            }
            DB::beginTransaction();
            if($kategori_id==-1){
                if(EdKategori::where('kategori_adi',$eklenecek)->withTrashed()->first()){
                    if($deleted=EdKategori::where('kategori_adi',$eklenecek)->onlyTrashed()->first()){
                        $deleted->restore();
                        $deleted->update([
                            'ust_id'=>null
                        ]);
                    }else{
                        DB::rollBack();
                        return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Girilen Alt Kategori Adı Eklenemedi', 'text' => 'Alt Kategori Adı Zaten Mevcut!', 'type' => 'error'));
                    }
                }else{
                    $altkategori = EdKategori::create(array(
                        'kategori_adi'=>$eklenecek,
                        'ust_id'=>null,
                        'slug'=>Str::slug($eklenecek)
                    ));
                }
                $title = 'Kategoriye Alt Kategori Eklendi';
                $text = 'Kategoriye Alt Kategori Başarıyla Eklendi';
            }else{
                $kategori = EdKategori::find($kategori_id);
                if($durum){ // alt kategori eklenecek
                    if(EdKategori::where('kategori_adi',$eklenecek)->withTrashed()->first()){
                        if($deleted=EdKategori::where('kategori_adi',$eklenecek)->onlyTrashed()->first()){
                            $deleted->restore();
                            $deleted->update([
                                'ust_id'=>$kategori_id
                            ]);
                        }else{
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Girilen Alt Kategori Adı Eklenemedi', 'text' => 'Alt Kategori Adı Zaten Mevcut!', 'type' => 'error'));
                        }
                    }else{
                        $altkategori = EdKategori::create(array(
                            'kategori_adi'=>$eklenecek,
                            'ust_id'=>$kategori_id,
                            'slug'=>Str::slug($eklenecek)
                        ));
                    }
                    $title = 'Kategoriye Alt Kategori Eklendi';
                    $text = 'Kategoriye Alt Kategori Başarıyla Eklendi';
                }else{ //urun eklenecek
                    if(EdUrun::where('urun_adi',$eklenecek)->withTrashed()->first()) {
                        if ($urun = EdUrun::where('urun_adi', $eklenecek)->onlyTrashed()->first()) {
                            $urun->restore();
                        } else {
                            DB::rollBack();
                            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Girilen Alt Kategori Adı Eklenemedi', 'text' => 'Alt Kategori Adı Zaten Mevcut!', 'type' => 'error'));
                        }
                    }else {
                        $urun = EdUrun::create(array(
                            'urun_adi' => $eklenecek,
                            'slug' => Str::slug($eklenecek)
                        ));
                    }
                    while($kategori->ust_id!=null){
                        EdKategoriUrun::create([
                            'kategori_id' => $kategori->ust_id,
                            'urun_id' => $urun->id
                        ]);
                        $kategori = EdKategori::find($kategori->ust_id);
                    }
                    EdKategoriUrun::create([
                        'kategori_id' => $kategori_id,
                        'urun_id' => $urun->id
                    ]);
                    $title = 'Kategoriye Ürün Eklendi';
                    $text = 'Kategoriye Ürün Başarıyla Eklendi';
                }
            }
            DB::commit();
            return Redirect::to('destekdatabase/kategoriurun')->with(array('mesaj' => 'true', 'title' => $title, 'text' => $text, 'type' => 'success'));
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Alt Kategori - Ürün Ekleme Yapılamadı', 'text' => 'Alt Kategori - Ürün Ekleme Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

    public function getKategorisil($id){
        try {
            DB::beginTransaction();
            $kategori = EdKategori::find($id);
            if($kategori){
                $bilgi = clone $kategori;
                $alt_kategori = EdKategori::where('ust_id', $id)->first();
                if ($alt_kategori) {
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kategori Silinemez', 'text' => 'Kategoriye ait Alt Kategori Mevcut!', 'type' => 'error'));
                }
                $urun = EdKategoriUrun::where('kategori_id',$id)->first();
                if ($urun){
                    DB::rollBack();
                    return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kategori Silinemez', 'text' => 'Kategoriye ait Ürün Mevcut!', 'type' => 'error'));
                }
                $kategori->delete();
                DB::commit();
                return Redirect::to('destekdatabase/kategoriurun')->with(array('mesaj' => 'true', 'title' => 'Kategori Silindi', 'text' => 'Kategori Başarıyla Silindi.', 'type' => 'success'));
            }else{
                return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kategori Silinemedi', 'text' => 'Kategori Zaten Silinmiş.', 'type' => 'error'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kategori Silinemedi', 'text' => 'Kategori Silinirken Sorun Oluştu.', 'type' => 'error'));
        }
    }

    public function getKategoriurunsil($urun){
        try {
            DB::beginTransaction();
            EdKategoriUrun::where('urun_id', $urun)->delete();
            EdUrun::find($urun)->delete();
            DB::commit();
            return Redirect::to('destekdatabase/kategoriurun')->with(array('mesaj' => 'true', 'title' => 'Kategoriden Ürün Silindi', 'text' => 'Kategoriden Ürün Başarıyla Silindi', 'type' => 'success'));
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            return Redirect::back()->with(array('mesaj' => 'true', 'title' => 'Kategoriden Ürün Silme Yapılamadı', 'text' => 'Kategoriden Ürün Silme Yapılırken Hata ile Karşılaşıldı.', 'type' => 'error'));
        }
    }

}
