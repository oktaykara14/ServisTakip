<?php

class EdestekKonuIslem extends Eloquent {

	protected $table = 'edestekkonuislem';
        public $timestamps = false;


    public function edestekduzenliislem()
    {
        return $this->hasMany('EdestekDuzenliIslem');
    }

    public function edestekislem()
    {
        return $this->hasMany('EdestekIslem');
    }

}
