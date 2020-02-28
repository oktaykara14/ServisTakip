<?php

class EdestekKonu extends Eloquent {

	protected $table = 'edestekkonu';
    public $timestamps = false;

    public function edestekhatacozum()
    {
        return $this->hasMany('EdestekHataCozum');
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
