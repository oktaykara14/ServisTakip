<?php

class EdestekKayit extends Eloquent {

	protected $table = 'edestekkayit';
        //public $timestamps = false;
        
        public function edestekmusteri()
        {
            return $this->belongsTo('EdestekMusteri')->withTrashed();
        }
        
        public function edestekkayitkonu()
        {
            return $this->belongsTo('EdestekKayitKonu');
        }
        
        public function edestekpersonel()
        {
            return $this->belongsTo('EdestekPersonel')->withTrashed();
        }
}
