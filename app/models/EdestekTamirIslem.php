<?php

class EdestekTamirIslem extends Eloquent {

	protected $table = 'edestektamirislem';
        public $timestamps = false;


    public function edestektamir()
    {
        return $this->hasMany('EdestekTamir');
    }
}
