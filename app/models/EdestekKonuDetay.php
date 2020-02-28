<?php

class EdestekKonuDetay extends Eloquent {

	protected $table = 'edestekkonudetay';
    public $timestamps = false;

    public function edestekhatacozum()
    {
        return $this->hasMany('EdestekHataCozum');
    }

    public function edestekgorusme()
    {
        return $this->hasMany('EdestekGorusme');
    }
}
