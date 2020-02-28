<?php

class Grup extends Eloquent {

	protected $table = 'grup';
        public $timestamps = false;
        
        public function kullanici()
        {
            return $this->hasMany('Kullanici');
        }

}
