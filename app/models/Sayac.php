<?php

class Sayac extends Eloquent {

	protected $table = 'sayac';
        public $timestamps = false;

    public function arizafiyat()
    {
        return $this->hasMany('ArizaFiyat');
    }
}
