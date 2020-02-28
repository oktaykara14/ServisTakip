<?php

class ArizaFiyat extends Eloquent {

	protected $table = 'arizafiyat';
        public $timestamps = false;

    public function sayacadi()
    {
        return $this->belongsTo('SayacAdi');
    }
    public function sayac()
    {
        return $this->belongsTo('Sayac');
    }
    public function parabirimi()
    {
        return $this->belongsTo('ParaBirimi');
    }
}
