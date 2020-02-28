<?php

class Iller extends Eloquent {

	protected $table = 'iller';
        public $timestamps = false;
        
        public function edestekmusteribilgi()
        {
            return $this->hasMany('EdestekMusteriBilgi');
        }

}
