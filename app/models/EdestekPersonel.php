<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class EdestekPersonel extends Eloquent {

    use SoftDeletingTrait;
    
	protected $table = 'edestekpersonel';
        public $timestamps = false;
        protected $dates = ['deleted_at'];
        public function kullanici()
        {
            return $this->belongsTo('Kullanici');
        }
        
        public function sonislem($sonislemid)
        {
            return EdestekKayit::find($sonislemid);
        }
        
        public function edestekislem()
        {
            return $this->hasMany('EdestekIslem');
        }
        
        public function ilgiler($list)
        {
            $ilgilist = explode(',', $list);
            $ilgiler = EdestekIlgi::whereIn('id',$ilgilist)->get();
            $ilgilersecilen="";
            foreach($ilgiler as $ilgi)
            {
               $ilgilersecilen .= ($ilgilersecilen=="" ? "" : ",").$ilgi->adi;
            }
            return $ilgilersecilen;
        }
        
        public function edestekhatacozum()
        {
            return $this->hasMany('EdestekHataCozum');
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
}
