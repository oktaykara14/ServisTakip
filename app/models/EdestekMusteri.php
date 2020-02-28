<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;
class EdestekMusteri extends Eloquent {
    
        use SoftDeletingTrait;
        
	protected $table = 'edestekmusteri';
        public $timestamps = false;
        protected $dates = ['deleted_at'];
        
        public function edestekmusteribilgi()
        {
            return $this->belongsTo('EdestekMusteriBilgi');
        }
        
        public function edesteksistembilgi()
        {
            return $this->belongsTo('EdestekSistemBilgi');
        }
        
        public function tumbaskilar()
        {
            if($this->edestekbaskiidler!=""){
                $baskilar = explode(',', $this->edestekbaskiidler);
                return EdestekKartBaski::whereIn('id',$baskilar)->get();
            }else{
                return "";
            }
        }
        
        public function digerbaskilar()
        {
            if($this->edestekbaskiidler!=""){
                $baskilar = explode(',', $this->edestekbaskiidler);
                $baskitur = EdestekKartBaski::whereIn('id',$baskilar)->lists('edestekbaskitur_id');
                return EdestekBaskiTur::whereNotIn('id', $baskitur)->get();
            }else{
                return EdestekBaskiTur::all();
            }
        }
        
        public function edestekkurulum()
        {
            return $this->hasMany('EdestekKurulum');
        }
        
        public function edestekkayit()
        {
            return $this->hasMany('EdestekKayit');
        }

        public function edestektamir()
        {
            return $this->hasMany('EdestekTamir');
        }

        public function edestekbaski()
        {
            return $this->hasMany('EdestekBaski');
        }

        public function edestekgorusme()
        {
            return $this->hasMany('EdestekGorusme');
        }


        public function edestekduzenliislem()
        {
            return $this->hasMany('EdestekDuzenliIslem');
        }

        public function edestekislem()
        {
            return $this->hasMany('EdestekIslem');
        }
}
