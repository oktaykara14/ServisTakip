<?php

class Fiyat extends Eloquent {

	protected $table = 'fiyat';
        public $timestamps = false;

        public function uretimyer()
        {
            return $this->belongsTo('UretimYer');
        }
        
        public function degisenler()
        {
            return $this->belongsTo('Degisenler');
        }
        
        public function parabirimi()
        {
            return $this->belongsTo('ParaBirimi');
        }
}
