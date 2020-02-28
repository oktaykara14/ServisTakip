<?php

class EdestekMusteriBilgi extends Eloquent {

	protected $table = 'edestekmusteribilgi';
        public $timestamps = false;
        
        public function edestekmusteri()
        {
            return $this->hasMany('EdestekMusteri');
        }
        
        public function iller()
        {
            return $this->belongsTo('Iller');
        }
}
