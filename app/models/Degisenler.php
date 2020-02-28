<?php

class Degisenler extends Eloquent {

	protected $table = 'degisenler';
        public $timestamps = false;
        
        public function fiyat()
        {
            return $this->hasMany('Fiyat');
        }
}
