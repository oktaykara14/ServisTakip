<?php

class EdestekTamirUrun extends Eloquent {

	protected $table = 'edestektamirurun';
    public $timestamps = false;


    public function edestektamir()
    {
        return $this->hasMany('EdestekTamir');
    }
}
