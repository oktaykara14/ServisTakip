<?php

class EdestekKayitKonu extends Eloquent {

	protected $table = 'edestekkayitkonu';
        public $timestamps = false;
        
        public function edestekkayit()
        {
            return $this->hasMany('EdestekKayit');
        }
}
