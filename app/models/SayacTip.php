<?php

class SayacTip extends Eloquent {

	protected $table = 'sayactip';
        public $timestamps = false;
        public function sayacmarka()
        {
            return $this->belongsTo('SayacMarka');
        }
        
        public function sayacadi()
        {
            return $this->hasMany('SayacAdi');
        }
}
