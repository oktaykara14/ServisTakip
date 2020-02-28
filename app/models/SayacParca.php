<?php

class SayacParca extends Eloquent {

	protected $table = 'sayacparca';
        public $timestamps = false;
        
        public function sayacadi()
        {
            return $this->belongsTo('SayacAdi');
        }
        
        public function sayaccap()
        {
            return $this->belongsTo('SayacCap');
        }

}
