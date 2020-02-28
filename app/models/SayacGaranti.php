<?php

class SayacGaranti extends Eloquent {

	protected $table = 'sayacgaranti';
        public $timestamps = false;
        
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
}
