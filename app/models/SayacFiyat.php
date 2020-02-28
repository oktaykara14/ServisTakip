<?php

class SayacFiyat extends Eloquent {

	protected $table = 'sayacfiyat';
        
        public function uretimyer()
        {
            return $this->belongsTo('UretimYer');
        }
        
        public function sayacadi()
        {
            return $this->belongsTo('SayacAdi');
        }
        
        public function sayaccap()
        {
            return $this->belongsTo('SayacCap');
        }
        
        public function parabirimi()
        {
            return $this->belongsTo('ParaBirimi');
        }
        
        public function kullanici()
        {
            return $this->belongsTo('Kullanici');
        }
}
